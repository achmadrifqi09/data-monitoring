<?php

namespace App\Http\Controllers;

use App\Models\Bill;
use App\Models\Payment;
use App\Models\PaymentInstallment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {

        $search = $request->input('search');
        $orderQuery = Payment::whereHas('payment_installments');

        if ($search) {
            $orderQuery->where(function ($query) use ($search) {
                $query->whereHas('order', function ($query) use ($search) {
                    $query->where('po_number', 'like', '%' . $search . '%');
                })
                    ->orWhereHas('bill', function ($query) use ($search) {
                        $query->where('bap', 'like', '%' . $search . '%');
                    });
            });
        }
        $payments = $orderQuery->with(['order', 'bill'])
            ->paginate(15);

        return view('pages.payment.index', [
            'payments' => $payments
        ]);
    }

    public function show(int $id)
    {

        $payment = Payment::where('id', $id)
            ->with([
                'order' => function ($query) {
                    $query->select('id', 'po_number');
                },
                'bill' => function ($query) {
                    $query->select('id', 'bap', 'netto');
                },
                'payment_installments'
            ])
            ->first();

        return view('pages.payment.detail', [
            'payment' => $payment
        ]);
    }

    public function store(Request $request)
    {
        try {
            $this->validateData($request);
            $bill = Bill::find($request->input('bill_id'));
            if (!$bill) {
                notify()->error('Data tagihan tidak ditemukan', 'Gagal');
                return redirect()->back();
            }

            $paymentTotal = floatval($request->input('payment_total'));
            $netto = floatval($bill->netto);

            $paymentPercentage = round(($paymentTotal / $netto) * 100, 1);
            $loanTotal = 0;
            if ($paymentTotal < $netto) $loanTotal = $netto - $paymentTotal;

            $payment = Payment::create([
                'order_id' => $bill->order_id,
                'bill_id' => $bill->id,
                'payment_total' =>  $paymentTotal,
                'payment_percentage' => $paymentPercentage,
                'loan_total' => $loanTotal
            ]);

            $paymentProofPath = null;
            if ($request->hasFile('payment_proof')) {
                $paymentProof = $request->file('payment_proof');
                $paymentProofPath = $this->storePaymentProof(
                    $paymentProof,
                    $request->input('payment_date'),
                    $paymentProof->getClientOriginalExtension()
                );
            }

            PaymentInstallment::create([
                'payment_id' => $payment->id,
                'payment_date' => $request->input('payment_date'),
                'nominal_payment' => $request->input('payment_total'),
                'payment_proof' => $paymentProofPath
            ]);

            notify()->success('Data pembayaran berhasil di tambahkan', 'Berhasil');
            return redirect()->back();
        } catch (ValidationException $e) {
            notify()->success($e->getMessage(), 'Gagal');
            return redirect()->back();
        }
    }

    public function updatePaymentInstallment(Request $request, int $payment_id, int $payment_installment_id)
    {
        $data = $this->validateData($request);
        $payment = Payment::where('id', $data['payment_id'])->with('bill')->first();
        $paymentInstallment = PaymentInstallment::find($payment_installment_id);

        if (!$paymentInstallment || !$payment) {
            notify()->error('Data pembayaran tidak ditemukan', 'Gagal');
            return redirect()->back();
        }

        $totalNominalPayment = floatval($payment->payment_total) - floatval($paymentInstallment->nominal_payment);
        $finalTotalNominalPayment = $totalNominalPayment + floatval($data['payment_total']);

        $netto = floatval($payment->bill->netto);
        $paymentPercentage = round(($finalTotalNominalPayment / $netto) * 100, 1);

        $loanTotal = 0;
        if ($finalTotalNominalPayment < $netto) $loanTotal = $netto - $finalTotalNominalPayment;

        $paymentInstallmentPayload = [
            'payment_id' => $payment->id,
            'payment_date' => $request->input('payment_date'),
            'nominal_payment' => $request->input('payment_total'),
        ];

        if ($request->hasFile('payment_proof')) {
            if ($paymentInstallment->payment_proof && Storage::exists($paymentInstallment->payment_proof)) {
                Storage::delete($paymentInstallment->payment_proof);
            }

            $paymentProof = $request->file('payment_proof');
            $paymentInstallmentPayload['payment_proof'] = $this->storePaymentProof(
                $paymentProof,
                $request->input('payment_date'),
                $paymentProof->getClientOriginalExtension()
            );
        }
        $paymentInstallment->update($paymentInstallmentPayload);

        $payment->update([
            'payment_total' => $finalTotalNominalPayment,
            'payment_percentage' => $paymentPercentage,
            'loan_total' => $loanTotal,
        ]);
        return redirect()->back();
    }

    public function addPaymentInstallment(Request $request)
    {
        try {
            $data = $this->validateData($request);
            $payment = Payment::where('id', $data['payment_id'])->with('bill')->first();
            if (!$payment) {
                notify()->error('Data pembayaran tidak ditemukan', 'Gagal');
                return redirect()->back();
            }

            $totalNominalPayment = PaymentInstallment::where('payment_id', $payment->id)
                ->sum('nominal_payment');
            $paymentTotal = floatval($totalNominalPayment) + floatval($data['payment_total']);
            $netto = floatval($payment->bill->netto);
            $paymentPercentage = round(($paymentTotal / $netto) * 100, 1);
            $loanTotal = 0;
            if ($paymentTotal < $netto) $loanTotal = $netto - $paymentTotal;

            $paymentProofPath = null;
            if ($request->hasFile('payment_proof')) {
                $paymentProof = $request->file('payment_proof');
                $paymentProofPath = $this->storePaymentProof(
                    $paymentProof,
                    $request->input('payment_date'),
                    $paymentProof->getClientOriginalExtension()
                );
            }

            PaymentInstallment::create([
                'payment_id' => $payment->id,
                'payment_date' => $data['payment_date'],
                'nominal_payment' => $data['payment_total'],
                'payment_proof' => $paymentProofPath
            ]);

            $payment->update([
                'payment_total' => $paymentTotal,
                'payment_percentage' => $paymentPercentage,
                'loan_total' => $loanTotal,
            ]);

            notify()->success('Data pembayaran berhasil di tambahkan', 'Berhasil');
            return redirect()->back();
        } catch (ValidationException $e) {
            notify()->error($e->getMessage(), 'Gagal');
            return redirect()->back();
        }
    }

    public function deletePaymentInstallment(int $payment_id, int $payment_installment_id)
    {
        $payment = Payment::where('id', $payment_id)->with('bill')->first();
        $paymentInstallment = PaymentInstallment::find($payment_installment_id);

        if (!$payment || !$paymentInstallment) {
            notify()->error('Data pembayaran tidak ditemukan', 'Gagal');
            return redirect()->back();
        }

        $totalNominalPayment = floatval($payment->payment_total) - floatval($paymentInstallment->nominal_payment);

        $netto = floatval($payment->bill->netto);
        $paymentPercentage = round(($totalNominalPayment / $netto) * 100, 1);

        $loanTotal = 0;
        if ($totalNominalPayment < $netto) $loanTotal = $netto - $totalNominalPayment;

        if ($paymentInstallment->payment_proof && Storage::exists($paymentInstallment->payment_proof)) {
            Storage::delete($paymentInstallment->payment_proof);
        }

        $paymentInstallment->delete();
        $payment->update([
            'payment_total' => $totalNominalPayment,
            'payment_percentage' => $paymentPercentage,
            'loan_total' => $loanTotal,
        ]);

        notify()->success('Berhasil menghapus data pembayaran', 'Berhasil');
        return redirect()->back();
    }

    private function validateData(Request $request)
    {
        $input = $request->except('_token');
        $rules = [
            'payment_date' => 'required|date',
            'payment_total' => 'required|numeric',
            'payment_proof' => 'nullable',
        ];
        $messages = [
            'payment_date.required' => 'Tanggal pembayaran harus diisi',
            'payment_date.date' => 'Tanggal pembayaran harus berformat tanggal',
            'payment_total.required' => 'Jumlah bayar harus diisi',
            'payment_total.numeric' => 'Jumlah bayar harus berupa angka',
        ];

        if ($request->path() !== '/payment/installment') {
            $rules[] = [
                'bill_id' => 'required|numeric',
            ];
            $messages[] = [
                'bill_id.required' => 'Tagihan harus diisi',
                'bill_id.numeric' => 'Tagihan tidak valid',
            ];
        }

        $validator = Validator::make($input, $rules, $messages);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            throw ValidationException::withMessages([
                'message' => $error,
            ]);
        }
        return $input;
    }

    public function uploadPaymentProof(Request $request, int $payment_id, int $payment_installment_id)
    {
        $validator = Validator::make($request->all(), [
            'payment_proof' => 'required|file|mimes:jpg,jpeg,png|max:10240'
        ], [
            'payment_proof.required' => 'File bukti pembayaran harus diisi',
            'payment_proof.file' => 'Yang Anda upload bukan file yang valid.',
            'payment_proof.mimes' => 'File bukti pembayaran harus berupa jpg, jpeg, png.',
            'payment_proof.max' => 'File bukti pembayaran maksimal 10MB.',
        ]);

        if ($validator->fails()) {
            $error = $validator->errors()->first();
            notify()->error($error, 'Gagal');
            return redirect()->back();
        }

        $paymentInstallment = PaymentInstallment::find($payment_installment_id);
        if (!$paymentInstallment) {
            notify()->error('Pembayaran tidak ditemukan', 'Gagal');
            return redirect()->back();
        }

        $file = $request->file('payment_proof');
        $paymentProofPath = $this->storePaymentProof(
            $file,
            $paymentInstallment->payment_date,
            $file->getClientOriginalExtension()
        );

        $paymentInstallment->update([
            'payment_proof' => $paymentProofPath
        ]);

        if ($paymentProofPath) {
            notify()->success('Berhasil mengunggah bukti pembayaran', 'Berhasil');
            return redirect()->back();
        } else {
            notify()->error('Terjadi kesalahan saat mengunggah file', 'Gagal');
            return redirect()->back();
        }
    }

    public function storePaymentProof($paymentProofFile, $paymentDate, $extendsion)
    {
        if (!$paymentProofFile) return null;

        $fileName = 'payment_' . Str::uuid() . '_' . $paymentDate . '.' . $extendsion;

        $directory = '/payment/payment-proof';
        $path = Storage::putFileAs($directory, $paymentProofFile, $fileName);
        return $path;
    }

    public function destroy(int $id)
    {
        $payment = Payment::find($id);
        $paymentInstallments = PaymentInstallment::where('payment_id', $id)->get();

        if (!$payment) {
            notify()->error('Data pembayaran tidak ditemukan', 'Gagal');
            return redirect()->back();
        }

        foreach ($paymentInstallments as $paymentInstallment) {
            if ($paymentInstallment->payment_proof && Storage::exists($paymentInstallment->payment_proof)) {
                Storage::delete($paymentInstallment->payment_proof);
            }
        }
        $payment->delete();
        notify()->success('Berhasil menghapus pembayaran', 'Berhasil');
        return redirect()->back();
    }
}

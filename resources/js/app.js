import "./bootstrap";
import "flowbite";

$(document).ready(() => {
    const darkModeButton = $("#darkModeButton");
    darkModeButton.click(() => {
        if (
            localStorage.getItem("theme") === "light" ||
            (!("theme" in localStorage) &&
                window.matchMedia("(prefers-color-scheme: light)").matches)
        ) {
            localStorage.setItem("theme", "dark");
            document.documentElement.classList.add("dark");
        } else {
            localStorage.setItem("theme", "light");
            document.documentElement.classList.remove("dark");
        }
    });
});

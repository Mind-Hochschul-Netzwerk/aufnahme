export function sendResizeMessage() {
    const style = window.getComputedStyle(document.querySelector("html"));
    window.parent.postMessage({
        name: "setHeight",
        height: parseInt(style.height) +
            parseInt(style.marginTop) +
            parseInt(style.marginBottom),
        origin: location.toString(),
    }, "*");
}
window.addEventListener("resize", sendResizeMessage);
window.addEventListener("load", sendResizeMessage);

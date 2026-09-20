const btn = document.querySelector('button[type=\"button\"]');
if(btn) {
    console.log(btn.outerHTML);
    console.log(window.getComputedStyle(btn).pointerEvents);
}

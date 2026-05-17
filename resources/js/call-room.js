const callRoot = document.querySelector('[data-call-root]');

if (callRoot) {
    const endCallButton = callRoot.querySelector('[data-end-call]');
    const callFrame = callRoot.querySelector('[data-call-frame]');
    const callStatus = callRoot.querySelector('[data-call-status]');

    callFrame?.addEventListener('load', () => {
        if (callStatus) {
            callStatus.textContent = 'Appel pret';
        }
    });

    endCallButton?.addEventListener('click', () => {
        window.history.back();
    });
}

const callRoot = document.querySelector('[data-call-root]');

if (callRoot) {
    const mode = callRoot.dataset.mode ?? 'video';
    const localVideo = callRoot.querySelector('[data-local-video]');
    const audioState = callRoot.querySelector('[data-audio-state]');
    const endCallButton = callRoot.querySelector('[data-end-call]');
    const micButton = callRoot.querySelector('[data-toggle-mic]');
    const cameraButton = callRoot.querySelector('[data-toggle-camera]');
    const speakerButton = callRoot.querySelector('[data-toggle-speaker]');

    let localStream = null;
    let speakerEnabled = true;

    const updateButtonState = (button, enabled, activeLabel, inactiveLabel) => {
        if (!button) {
            return;
        }

        button.textContent = enabled ? activeLabel : inactiveLabel;
        button.classList.toggle('bg-emerald-400/20', enabled);
        button.classList.toggle('border-emerald-300/30', enabled);
        button.classList.toggle('text-emerald-100', enabled);
    };

    const stopStream = () => {
        if (!localStream) {
            return;
        }

        localStream.getTracks().forEach((track) => track.stop());
        localStream = null;
    };

    const attachStream = async () => {
        try {
            localStream = await navigator.mediaDevices.getUserMedia({
                audio: true,
                video: mode === 'video',
            });

            if (localVideo) {
                localVideo.srcObject = localStream;
            }

            if (audioState) {
                audioState.classList.toggle('hidden', mode !== 'audio');
            }

            updateButtonState(micButton, true, 'Micro on', 'Micro off');
            updateButtonState(cameraButton, mode === 'video', 'Camera on', 'Camera off');
            updateButtonState(speakerButton, speakerEnabled, 'Haut-parleur on', 'Haut-parleur off');
        } catch (error) {
            if (audioState) {
                audioState.classList.remove('hidden');
                audioState.textContent = "Autorise le micro et la camera pour afficher l'aperçu local.";
            }
        }
    };

    const toggleTrack = (kind) => {
        if (!localStream) {
            return;
        }

        const track = localStream.getTracks().find((item) => item.kind === kind);

        if (!track) {
            return;
        }

        track.enabled = !track.enabled;

        if (kind === 'audio') {
            updateButtonState(micButton, track.enabled, 'Micro on', 'Micro off');
        }

        if (kind === 'video') {
            updateButtonState(cameraButton, track.enabled, 'Camera on', 'Camera off');

            if (audioState) {
                audioState.classList.toggle('hidden', track.enabled);
                if (!track.enabled) {
                    audioState.textContent = 'Camera coupee. Le micro reste actif.';
                }
            }
        }
    };

    endCallButton?.addEventListener('click', () => {
        stopStream();
        window.history.back();
    });

    micButton?.addEventListener('click', () => toggleTrack('audio'));
    cameraButton?.addEventListener('click', () => toggleTrack('video'));
    speakerButton?.addEventListener('click', () => {
        speakerEnabled = !speakerEnabled;
        updateButtonState(speakerButton, speakerEnabled, 'Haut-parleur on', 'Haut-parleur off');
    });

    window.addEventListener('beforeunload', stopStream);

    attachStream();
}

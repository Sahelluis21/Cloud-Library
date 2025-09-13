document.addEventListener("DOMContentLoaded", function () {
    const uploadButton = document.querySelector(".upload-button");
    const fileInput = document.getElementById("fileToUpload");

    // Cria pop-up de progresso (adicionado ao body)
    const progressPopup = document.createElement("div");
    progressPopup.id = "progressPopup";
    progressPopup.innerHTML = `
        <div class="progress-container">
            <p id="progressText">Enviando arquivo...</p>
            <div class="progress-bar-bg">
                <div class="progress-bar-fill" style="width: 0%;"></div>
            </div>
        </div>
    `;
    document.body.appendChild(progressPopup);

    // Estilo mínimo (você pode transferir isso para seu CSS)
    const popupStyle = document.createElement("style");
    popupStyle.textContent = `
        #progressPopup {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 0 12px rgba(0,0,0,0.2);
            padding: 16px 20px;
            z-index: 9999;
            width: 300px;
            display: none;
        }
        .progress-container p {
            margin: 0 0 8px;
            font-weight: bold;
        }
        .progress-bar-bg {
            width: 100%;
            height: 14px;
            background: #eee;
            border-radius: 8px;
            overflow: hidden;
        }
        .progress-bar-fill {
            height: 100%;
            background: #28a745;
            width: 0%;
            transition: width 0.2s;
        }
    `;
    document.head.appendChild(popupStyle);

    // Intercepta a mudança no input e envia via AJAX
    fileInput.addEventListener("change", function () {
        const file = fileInput.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append("fileToUpload", file);

        const xhr = new XMLHttpRequest();
        xhr.open("POST", "/configarq/upload.php", true);

        // Mostra pop-up
        progressPopup.style.display = "block";

        // Atualiza barra de progresso
        xhr.upload.onprogress = function (event) {
            if (event.lengthComputable) {
                const percent = Math.round((event.loaded / event.total) * 100);
                progressPopup.querySelector(".progress-bar-fill").style.width = percent + "%";
                progressPopup.querySelector("#progressText").textContent = `Enviando: ${percent}%`;
            }
        };

        // Sucesso
        xhr.onload = function () {
            if (xhr.status === 200) {
                const res = JSON.parse(xhr.responseText);
                if (res.success) {
                    location.reload(); // Recarrega imediatamente
                } else {
                    progressPopup.querySelector("#progressText").textContent = "Erro: " + res.error;
                }
            } else {
                progressPopup.querySelector("#progressText").textContent = "Erro no envio (servidor).";
            }
        };

        // Erro geral
        xhr.onerror = function () {
            progressPopup.querySelector("#progressText").textContent = "Erro ao enviar o arquivo.";
        };

        xhr.send(formData);
    });
});

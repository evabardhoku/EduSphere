const form = document.querySelector(".typing-area"),
    incoming_id = form.querySelector(".incoming_id").value,
    inputField = form.querySelector(".input-field"),
    sendBtn = form.querySelector("button[type='submit']"),
    fileBtn = form.querySelector("button#fileButton"),
    chatBox = document.querySelector(".chat-box"),
    fileInput = document.getElementById('fileInput');

let outgoing_id = ''; // Define this variable. Set it according to your logic.
let isUserScrolling = false; // Flag to track user scrolling

// Function to get the outgoing_id if needed (e.g., from a hidden input or server)
function getOutgoingId() {
    const outgoingElement = document.querySelector(".outgoing_id");
    if (outgoingElement) {
        outgoing_id = outgoingElement.value;
    } else {
        console.error("Element with class 'outgoing_id' not found.");
    }
}

// Call this function at the beginning to ensure outgoing_id is set
getOutgoingId();

form.onsubmit = (e) => {
    e.preventDefault();
};

inputField.focus();
inputField.onkeyup = () => {
    if (inputField.value.trim() !== "") {
        sendBtn.classList.add("active");
    } else {
        sendBtn.classList.remove("active");
    }
};

sendBtn.onclick = () => {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/insert-chat.php", true);
    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            inputField.value = "";
            if (!isUserScrolling) {
                scrollToBottom();
            }
        }
    };
    let formData = new FormData(form);
    xhr.send(formData);
};

fileBtn.onclick = (e) => {
    e.preventDefault();
    fileInput.click();
};

fileInput.addEventListener('change', function() {
    if (this.files.length > 0) {
        let formData = new FormData();
        for (let i = 0; i < this.files.length; i++) {
            formData.append('files[]', this.files[i]);
        }
        formData.append('incoming_id', incoming_id);
        formData.append('outgoing_id', outgoing_id);

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "php/upload-file.php", true);
        xhr.onload = () => {
            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.status === 'success') {
                        response.files.forEach(fileMessage => {
                            let fileElement = document.createElement('div');
                            // Check if the file is an image to display it
                            if (fileMessage.toLowerCase().endsWith('.jpg') ||
                                fileMessage.toLowerCase().endsWith('.jpeg') ||
                                fileMessage.toLowerCase().endsWith('.png') ||
                                fileMessage.toLowerCase().endsWith('.gif')) {
                                fileElement.innerHTML = `<img src="php/uploads/${fileMessage}" alt="${fileMessage}" style="max-width: 200px; max-height: 200px;">`;
                            } else {
                                fileElement.innerHTML = `<p>${fileMessage}</p>`;
                            }
                            chatBox.appendChild(fileElement);
                        });
                        if (!isUserScrolling) {
                            scrollToBottom();
                        }
                    } else {
                        console.error(response.message);
                    }
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                    console.error('Response text:', xhr.responseText); // Log the raw response text
                }
            } else {
                console.error('Request failed. Status:', xhr.status);
            }
        };
        xhr.send(formData);
    }
});

chatBox.onmouseenter = () => {
    chatBox.classList.add("active");
};

chatBox.onmouseleave = () => {
    chatBox.classList.remove("active");
};

chatBox.addEventListener('scroll', () => {
    const scrollTop = chatBox.scrollTop;
    const scrollHeight = chatBox.scrollHeight;
    const clientHeight = chatBox.clientHeight;

    // Check if user is scrolling up
    if (scrollTop + clientHeight < scrollHeight) {
        isUserScrolling = true;
    } else {
        isUserScrolling = false;
    }
});

setInterval(() => {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "php/get-chat.php", true);
    xhr.onload = () => {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            let data = xhr.response;
            chatBox.innerHTML = data;
            if (!isUserScrolling) {
                scrollToBottom();
            }
        }
    };
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.send("incoming_id=" + incoming_id);
}, 500);

function scrollToBottom() {
    chatBox.scrollTop = chatBox.scrollHeight;
}

function deleteMessage(msgId) {
    if (confirm("Are you sure you want to delete this message?")) {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "php/delete-message.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onload = () => {
            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.status === 'success') {
                    // Remove the message element from the chat
                    document.querySelector(`[data-msg-id="${msgId}"]`).remove();
                } else {
                    alert(response.message);
                }
            } else {
                console.error('Request failed. Status:', xhr.status);
            }
        };
        xhr.send("msg_id=" + msgId);
    }
}


let editMessageId = null;


function editMessage(msg_id) {
    editMessageId = msg_id;
    const messageElement = document.querySelector(`.chat[data-msg-id='${msg_id}'] .details`);
    const rect = messageElement.getBoundingClientRect();
    const editForm = document.getElementById('editForm');

    // Position the edit form near the message
    editForm.style.top = `${rect.top + window.scrollY}px`;
    editForm.style.left = `${rect.left + window.scrollX}px`;

    // Populate the edit form with the message text
    const messageText = messageElement.querySelector('p') ? messageElement.querySelector('p').innerText : '';
    document.getElementById('editMessage').value = messageText;

    // Show the edit form
    editForm.style.display = 'block';
}


function saveEdit() {
    const editedMessage = document.getElementById('editMessage').value;
    if (editMessageId && editedMessage) {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "php/edit-message.php", true);
        xhr.onload = () => {
            if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
                const response = xhr.responseText;
                if (response === "Message updated successfully") {
                    document.getElementById('editForm').style.display = 'none';
                    // Update the message in the chat box
                    document.querySelector(`.chat[data-msg-id='${editMessageId}'] .details p`).innerText = editedMessage;
                    editMessageId = null;
                } else {
                    alert(response); // Show the alert with the error message
                }
            } else {
                console.error('Request failed. Status:', xhr.status);
            }
        };
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.send("msg_id=" + editMessageId + "&msg=" + encodeURIComponent(editedMessage));
    }
}


function cancelEdit() {
    document.getElementById('editForm').style.display = 'none';
}


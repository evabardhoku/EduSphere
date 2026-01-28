<?php defined('APP') or die('direct script access denied!'); ?>

<div class="js-post-edit-modal class_55 hide" ; style="min-width: 600px; min-height: 200px">
    <div class="class_39" style="float:right; margin: 10px;padding:5px;padding-left:10px;padding-right:10px;" onclick="postedit.hide()">X</div>
    <h1 class="class_27"  >
        Edit Post
    </h1>

    <form onsubmit="postedit.submit(event)" method="post" class="class_42">
        <input type="hidden" name="id" class="js-post-id">
        <div class="class_43">
            <label>
                <textarea placeholder="Whats on your mind?" name="post" class="js-post-input class_44"></textarea>
            </label>
        </div>
        <div class="class_45">
            <button class="class_46">Update Post</button>
        </div>
    </form>

</div>
<script>
    var postedit = {
        show: function(id) {
            // Fetch the post element and its content
            postedit.edit_id = id;

            let data = document.querySelector("#post_" + id).getAttribute("row");
            data = data.replaceAll('\\"', '"');
            data = JSON.parse(data);

            // Set the textarea value and the post ID in the modal
            if (typeof data == 'object') {
                document.querySelector(".js-post-edit-modal .js-post-input").value = data.post;
                document.querySelector(".js-post-edit-modal .js-post-id").value = id; // Set hidden input value
            } else {
                alert("Invalid post data");
            }
            // Show the post edit modal and hide other modals
            document.querySelector(".js-post-edit-modal").classList.remove('hide');
            document.querySelector(".js-login-modal").classList.add('hide');
            document.querySelector(".js-signup-modal").classList.add('hide');
        },

        hide: function() {
            document.querySelector(".js-post-edit-modal").classList.add('hide');
        },

        submit: function(e) {
            e.preventDefault();

            // Get the post ID and updated content
            let id = document.querySelector(".js-post-edit-modal .js-post-id").value; // Use hidden input value
            let updated_content = document.querySelector(".js-post-edit-modal .js-post-input").value.trim();

            if (updated_content === "") {
                alert("Please type something to update the post");
                return;
            }

            // Prepare FormData for AJAX request
            let form = new FormData();
            form.append('id', id);
            form.append('post', updated_content);
            form.append('data_type', 'edit_post');

            // Create and configure XMLHttpRequest
            var ajax = new XMLHttpRequest();

            ajax.addEventListener('readystatechange', function() {
                if (ajax.readyState === 4) {
                    if (ajax.status === 200) {
                        console.log('Response Text:', ajax.responseText);

                        let obj = {};

                        // Attempt to parse JSON only if the responseText is not empty
                        if (ajax.responseText.trim() !== '') {
                            obj = JSON.parse(ajax.responseText);
                        }

                        if (obj && obj.success) {
                            alert(obj.message);

                            // Update the post content on the page immediately
                            document.getElementById('post_' + id).querySelector('.js-post').innerHTML = updated_content;

                            postedit.hide();  // Hide the modal
                        } else {
                            alert("Error: " + (obj.message || "Unexpected error occurred."));
                        }
                    } else {
                        alert("Error: " + ajax.status);
                    }
                }
            });

            // Open the POST request to the correct URL
            ajax.open('POST', 'ajax.inc.php', true);

            // Send the form data
            ajax.send(form);

            // Ensure the correct function is called to hide the modal
            document.querySelector(".class_39").setAttribute('onclick', 'postedit.hide()');
        }
    };

</script>

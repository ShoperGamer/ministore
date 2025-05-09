document.addEventListener('DOMContentLoaded', function() {
    console.log('ระบบจัดการสินค้าพร้อมใช้งาน');

    const imageInput = document.getElementById('image');
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById('image-preview');
                    const previewContainer = document.getElementById('image-preview-container');

                    if (preview) {
                        preview.src = event.target.result;
                        if (previewContainer) {
                            previewContainer.style.display = 'block';
                        }
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // Form validation
    (function () {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })();
});

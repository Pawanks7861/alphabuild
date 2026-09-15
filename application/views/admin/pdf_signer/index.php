<?php defined('BASEPATH') or exit('No direct script access allowed');  ?>

<?php init_head(); ?>

<div id="wrapper">

    <div class="content">

        <div class="panel_s">

            <div class="panel-body">

                <h4 class="tw-font-semibold">
                    PDF Signer
                </h4>

                <hr>

                <form
                    id="pdfUploadForm"
                    enctype="multipart/form-data"
                >

                    <div class="form-group">

                        <label>
                            Upload PDF
                        </label>

                        <input
                            type="file"
                            name="pdf_file"
                            id="pdf_file"
                            class="form-control"
                            accept="application/pdf"
                            required
                        >

                    </div>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        Upload PDF
                    </button>

                </form>

            </div>

        </div>

    </div>

</div>

<?php init_tail(); ?>

<script>

$('#pdfUploadForm').on('submit', function(e) {

    e.preventDefault();

    var csrf_token_value = '<?php echo $this->security->get_csrf_hash(); ?>'; // Replace with your actual CSRF token value
    var formData = new FormData(this);
    formData.append('csrf_token_name', csrf_token_value); // Replace with your actual CSRF token name and value

    $.ajax({

        url: admin_url + 'pdf_signer/upload',

        type: 'POST',

        data: formData,

        processData: false,

        contentType: false,

        dataType: 'json',

        beforeSend: function() {

            $('button[type="submit"]')
                .prop('disabled', true)
                .text('Uploading...');

        },

        success: function(response) {

            if (response.success) {

                window.location.href =
                    admin_url +
                    'pdf_signer/sign/' +
                    response.document_id;

            } else {

                alert(response.message);

            }

        },

        complete: function() {

            $('button[type="submit"]')
                .prop('disabled', false)
                .text('Upload PDF');

        }

    });

});

</script>
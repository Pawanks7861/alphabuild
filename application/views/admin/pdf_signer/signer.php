<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php init_head(); ?>

<style>
    .pdf-signer-wrapper {
        display: flex;
        gap: 15px;
    }

    .pdf-toolbar {
        width: 220px;
        background: #fff;
        border: 1px solid #ddd;
        padding: 15px;
        position: sticky;
        top: 20px;
        height: fit-content;
    }

    .pdf-toolbar button {
        width: 100%;
        margin-bottom: 10px;
    }

    .pdf-pages {
        flex: 1;
        background: #e9ecef;
        padding: 25px;
    }

    .pdf-page {
        position: relative;
        margin: 0 auto 25px;
        background: #fff;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .15);
    }

    .pdf-page canvas {
        display: block;
        width: 100%;
    }

    .sign-element {

        position: absolute;

        border: 2px dashed #007bff;

        cursor: move;

        z-index: 10;

        user-select: none;

    }

    .sign-element img {

        width: 100%;

        height: 100%;

        object-fit: contain;

        pointer-events: none;

    }

    .sign-element .remove-element {

        position: absolute;

        top: -12px;

        right: -12px;

        width: 25px;

        height: 25px;

        border-radius: 50%;

        background: red;

        color: white;

        border: 0;

        cursor: pointer;

    }
</style>

<div id="wrapper">

    <div class="content">

        <div class="panel_s">

            <div class="panel-body">

                <h4>
                    PDF Signer
                </h4>

                <hr>

                <div class="pdf-signer-wrapper">

                    <div class="pdf-toolbar">

                        <button
                            type="button"
                            id="addSignature"
                            class="btn btn-primary">
                            ✍ Add Signature
                        </button>

                        <button
                            type="button"
                            id="addStamp"
                            class="btn btn-info">
                            🏷 Add Stamp
                        </button>

                        <hr>

                        <button
                            type="button"
                            id="savePdf"
                            class="btn btn-success">
                            ✓ Sign & Save PDF
                        </button>

                    </div>

                    <div
                        class="pdf-pages"
                        id="pdfPages"></div>

                </div>

            </div>

        </div>

    </div>

</div>

<?php init_tail(); ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.10.38/pdf.min.mjs"
    type="module"></script>

<script src="https://cdn.jsdelivr.net/npm/interactjs/dist/interact.min.js"></script>

<script>
    const documentId =
        <?= (int)$document->id ?>;

    const pdfUrl =
        <?= json_encode(
            base_url($document->original_file)
        ) ?>;

    const signatureUrl =
        <?= json_encode($signature) ?>;

    const stampUrl =
        <?= json_encode($stamp) ?>;

    let elements = [];

    let pdfDocument = null;

    let pageScale = 1;
</script>
<script type="module">
    import * as pdfjsLib
    from 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.10.38/pdf.min.mjs';

    pdfjsLib.GlobalWorkerOptions.workerSrc =
        'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.10.38/pdf.worker.min.mjs';


    async function loadPDF() {
        pdfDocument =
            await pdfjsLib.getDocument(pdfUrl).promise;

        for (
            let pageNumber = 1; pageNumber <= pdfDocument.numPages; pageNumber++
        ) {

            await renderPage(pageNumber);

        }
    }


    async function renderPage(pageNumber) {
        const page =
            await pdfDocument.getPage(pageNumber);

        const viewport =
            page.getViewport({
                scale: 1.5
            });

        const pageDiv =
            document.createElement('div');

        pageDiv.className =
            'pdf-page';

        pageDiv.dataset.page =
            pageNumber;

        pageDiv.style.width =
            viewport.width + 'px';

        pageDiv.style.height =
            viewport.height + 'px';


        const canvas =
            document.createElement('canvas');

        canvas.width =
            viewport.width;

        canvas.height =
            viewport.height;

        pageDiv.appendChild(canvas);

        document
            .getElementById('pdfPages')
            .appendChild(pageDiv);


        const context =
            canvas.getContext('2d');


        await page.render({

            canvasContext: context,

            viewport: viewport

        }).promise;
    }


    loadPDF();
</script>

<script>
    $('#savePdf').on('click', function() {

        const elements =
            collectElements();


        if (!elements.length) {

            alert(
                'Please add signature or stamp first.'
            );

            return;

        }


        const button =
            $(this);


        button
            .prop('disabled', true)
            .text('Creating PDF...');


        $.ajax({

            url: admin_url +
                'pdf_signer/save_signed',

            type: 'POST',

            dataType: 'json',

            data: {

                document_id: documentId,

                elements: JSON.stringify(elements),

                csrf_token_name: $('input[name="csrf_token_name"]').val()

            },

            success: function(response) {

                if (response.success) {

                    window.location.href =
                        response.download_url;

                } else {

                    alert(
                        response.message
                    );

                }

            },

            error: function(xhr) {

                console.log(
                    xhr.responseText
                );

                alert(
                    'Unable to create signed PDF.'
                );

            },

            complete: function() {

                button
                    .prop('disabled', false)
                    .text('Sign & Save PDF');

            }

        });

    });
</script>

<script>
    $('#addSignature').on('click', function() {

        addElement(
            'signature',
            signatureUrl
        );

    });


    $('#addStamp').on('click', function() {

        addElement(
            'stamp',
            stampUrl
        );

    });


    function addElement(type, imageUrl) {
        const pages =
            document.querySelectorAll('.pdf-page');

        if (!pages.length) {

            alert('PDF is still loading.');

            return;

        }


        // Add to first page initially
        const page =
            pages[0];


        const element =
            document.createElement('div');

        element.className =
            'sign-element';


        element.dataset.type =
            type;


        element.dataset.page =
            page.dataset.page;


        if (type === 'signature') {

            element.style.width =
                '180px';

            element.style.height =
                '70px';

        } else {

            element.style.width =
                '120px';

            element.style.height =
                '120px';

        }


        element.style.left =
            '100px';

        element.style.top =
            '100px';


        const image =
            document.createElement('img');

        image.src =
            imageUrl;


        const remove =
            document.createElement('button');

        remove.innerHTML =
            '×';

        remove.className =
            'remove-element';


        remove.onclick = function(e) {

            e.stopPropagation();

            element.remove();

        };


        element.appendChild(image);

        element.appendChild(remove);

        page.appendChild(element);


        makeDraggable(element);
    }
</script>
<script>
    function makeDraggable(element) {
        interact(element)

            .draggable({

                modifiers: [

                    interact.modifiers.restrictRect({

                        restriction: 'parent',

                        endOnly: true

                    })

                ],

                listeners: {

                    move(event) {

                        const target =
                            event.target;


                        let x =
                            (parseFloat(
                                target.dataset.x
                            ) || 0) +
                            event.dx;


                        let y =
                            (parseFloat(
                                target.dataset.y
                            ) || 0) +
                            event.dy;


                        target.style.transform =
                            `translate(${x}px, ${y}px)`;


                        target.dataset.x =
                            x;

                        target.dataset.y =
                            y;

                    }

                }

            })

            .resizable({

                edges: {

                    left: true,

                    right: true,

                    bottom: true,

                    top: true

                },

                modifiers: [

                    interact.modifiers.restrictSize({

                        min: {

                            width: 50,

                            height: 30

                        }

                    })

                ],

                listeners: {

                    move(event) {

                        let target =
                            event.target;


                        let x =
                            parseFloat(
                                target.dataset.x
                            ) || 0;


                        let y =
                            parseFloat(
                                target.dataset.y
                            ) || 0;


                        target.style.width =
                            event.rect.width + 'px';


                        target.style.height =
                            event.rect.height + 'px';


                        x += event.deltaRect.left;

                        y += event.deltaRect.top;


                        target.style.transform =
                            `translate(${x}px, ${y}px)`;


                        target.dataset.x =
                            x;

                        target.dataset.y =
                            y;

                    }

                }

            });
    }
</script>
<script>
    function collectElements() {
        const output = [];

        document
            .querySelectorAll('.sign-element')
            .forEach(function(element) {

                const page =
                    element.closest('.pdf-page');

                const canvas =
                    page.querySelector('canvas');


                const pageWidth =
                    page.offsetWidth;

                const pageHeight =
                    page.offsetHeight;


                const pageNumber =
                    parseInt(
                        page.dataset.page
                    );


                const x =
                    parseFloat(
                        element.offsetLeft
                    ) +
                    (
                        parseFloat(
                            element.dataset.x
                        ) || 0
                    );


                const y =
                    parseFloat(
                        element.offsetTop
                    ) +
                    (
                        parseFloat(
                            element.dataset.y
                        ) || 0
                    );


                const width =
                    element.offsetWidth;


                const height =
                    element.offsetHeight;


                /*
                 * PDF physical size.
                 *
                 * FPDI uses mm.
                 */

                const pdfWidth =
                    210;

                const pdfHeight =
                    297;


                const pdfX =
                    (x / pageWidth) *
                    pdfWidth;


                const pdfY =
                    (y / pageHeight) *
                    pdfHeight;


                const pdfElementWidth =
                    (width / pageWidth) *
                    pdfWidth;


                const pdfElementHeight =
                    (height / pageHeight) *
                    pdfHeight;


                output.push({

                    type: element.dataset.type,

                    page: pageNumber,

                    x: pdfX,

                    y: pdfY,

                    width: pdfElementWidth,

                    height: pdfElementHeight

                });

            });


        return output;
    }
</script>
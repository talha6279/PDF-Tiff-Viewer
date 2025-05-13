<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Image and PDF Preview</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <script src="https://unpkg.com/tiff.js@1.0.0/tiff.min.js"></script>

    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }
        .toolbar {
            margin-bottom: 10px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }
        .toolbar button {
            background-color: #007bff;
            border: none;
            color: white;
            padding: 10px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
        }
        .toolbar button:hover {
            background-color: #0056b3;
        }
        .preview-container {
            position: relative;
            width: 800px;
            height: 600px;
            overflow: hidden;
            margin: auto;
            background-color: white;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .preview-container img,
        .preview-container iframe,
        .preview-container canvas {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            transition: all 0.3s ease;
        }
        .fallback-message {
            color: red;
            font-size: 18px;
            text-align: center;
        }
        .thumbnail-container {
            margin-top: 10px;
            text-align: center;
        }
        .thumbnail-container img {
            width: 150px;
            height: 150px;
            cursor: pointer;
            object-fit: cover;
            border: 2px solid #ddd;
            border-radius: 5px;
        }
        .thumbnail-container img:hover {
            border-color: #007bff;
        }
        select, input[type="range"] {
            padding: 8px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
    </style>
</head>
<body>

<div class="toolbar">
    <button id="print" title="Print"><i class="fas fa-print"></i></button>
    <button id="zoomIn" title="Zoom In"><i class="fas fa-search-plus"></i></button>
    <button id="zoomOut" title="Zoom Out"><i class="fas fa-search-minus"></i></button>
    <button id="rotateLeft" title="Rotate Left"><i class="fas fa-undo"></i></button>
    <button id="rotateRight" title="Rotate Right"><i class="fas fa-redo"></i></button>
    <button id="rotate180" title="Rotate 180°"><i class="fas fa-sync-alt"></i></button>
    <button id="widthFix" title="Width Fix"><i class="fas fa-arrows-alt-h"></i></button>
    <button id="heightFix" title="Height Fix"><i class="fas fa-arrows-alt-v"></i></button>
    <button id="bothFix" title="Height & Width Fix"><i class="fas fa-expand-arrows-alt"></i></button>
    <button id="toggleThumbnail" title="Toggle Thumbnail"><i class="fas fa-th-large"></i></button>

    <select id="filterSelect">
        <option value="">Select Filter</option>
        <option value="invert">Invert Colors</option>
        <option value="brightness">Brightness</option>
    </select>
    <input type="range" id="filterRange" min="0" max="100" value="0" style="display:none;">
</div>

<div class="preview-container" id="previewContainer">
    <div class="fallback-message" id="fallbackMessage">Loading Preview...</div>
</div>

<div class="thumbnail-container" id="thumbnailContainer" style="display: none;">
    <!-- Thumbnails for Image or PDF will load here -->
</div>


<script>
    const previewContainer = document.getElementById('previewContainer');
    const fallbackMessage = document.getElementById('fallbackMessage');
    const thumbnailContainer = document.getElementById('thumbnailContainer');
    const toggleThumbnailButton = document.getElementById('toggleThumbnail');
    const filterSelect = document.getElementById('filterSelect');
    const filterRange = document.getElementById('filterRange');

    const filePath = "{{ asset($picture->picture) }}";
    const fileExtension = filePath.split('.').pop().toLowerCase();

    let img = null;
    let iframe = null;
    let tiffPages = [];
    let currentTiffIndex = 0;
    let scale = 1;
    let rotate = 0;
    let invertValue = 0;
    let brightnessValue = 30;

    function loadPreview() {
        if (fileExtension === 'pdf') {
            iframe = document.createElement('iframe');
            iframe.src = filePath;
            iframe.width = '100%';
            iframe.height = '100%';
            iframe.style.border = 'none';
            previewContainer.appendChild(iframe);
            fallbackMessage.style.display = 'none';
        } else if (fileExtension === 'tiff' || fileExtension === 'tif') {
            loadTiffPreview();
        } else {
            img = document.createElement('img');
            img.src = filePath;
            img.id = "previewImage";
            img.style.objectFit = "contain";
            img.style.cursor = "pointer";
            img.addEventListener('dblclick', resetAll);
            previewContainer.appendChild(img);
            fallbackMessage.style.display = 'none';
            createThumbnail(img.src);
        }
    }

    function loadTiffPreview() {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", filePath, true);
        xhr.responseType = "arraybuffer";
        xhr.onload = function () {
            const tiff = new Tiff({buffer: xhr.response});
            const totalPages = tiff.countDirectory();
            tiffPages = [];

            for (let i = 0; i < totalPages; i++) {
                tiff.setDirectory(i);
                const canvas = tiff.toCanvas();
                tiffPages.push(canvas);
                createTiffThumbnail(canvas, i);
            }

            fallbackMessage.style.display = 'none';
            showTiffPage(0);
        };
        xhr.send();
    }

    function createTiffThumbnail(canvas, pageIndex) {
        const thumbnailCanvas = document.createElement('canvas');
        const context = thumbnailCanvas.getContext('2d');
        const thumbnailWidth = 150;
        const thumbnailHeight = 150;

        thumbnailCanvas.width = thumbnailWidth;
        thumbnailCanvas.height = thumbnailHeight;

        context.drawImage(canvas, 0, 0, canvas.width, canvas.height, 0, 0, thumbnailWidth, thumbnailHeight);

        const thumbnailImg = new Image();
        thumbnailImg.src = thumbnailCanvas.toDataURL();
        thumbnailImg.onclick = () => showTiffPage(pageIndex);
        thumbnailContainer.appendChild(thumbnailImg);
    }

    function showTiffPage(index) {
        currentTiffIndex = index;
        previewContainer.innerHTML = '';
        previewContainer.appendChild(tiffPages[index]);
        resetAll();
    }

    function createThumbnail(imageSrc) {
        const thumbnail = document.createElement('img');
        thumbnail.src = imageSrc;
        thumbnail.onclick = () => resetImagePreview();
        thumbnailContainer.appendChild(thumbnail);
    }

    function resetImagePreview() {
        if (img) {
            previewContainer.innerHTML = '';
            previewContainer.appendChild(img);
        }
    }

    function updateTransform() {
        const element = previewContainer.querySelector('img, iframe, canvas');
        if (element) {
            element.style.transform = `scale(${scale}) rotate(${rotate}deg)`;
        }
    }

    function updateFilter() {
        const element = previewContainer.querySelector('img, canvas, iframe');
        if (element) {
            element.style.filter = `invert(${invertValue}%) brightness(${brightnessValue}%)`;
        }
    }

    function resetAll() {
        scale = 1;
        rotate = 0;
        invertValue = 0;
        brightnessValue = 30;
        updateTransform();
        updateFilter();
        const element = previewContainer.querySelector('img, canvas, iframe');
        if (element) {
            element.style.objectFit = "contain";
            element.style.width = "100%";
            element.style.height = "100%";
        }
    }

    document.getElementById('print').onclick = () => window.print();
    document.getElementById('zoomIn').onclick = () => { scale += 0.1; updateTransform(); };
    document.getElementById('zoomOut').onclick = () => { scale = Math.max(0.1, scale - 0.1); updateTransform(); };
    document.getElementById('rotateLeft').onclick = () => { rotate -= 90; updateTransform(); };
    document.getElementById('rotateRight').onclick = () => { rotate += 90; updateTransform(); };
    document.getElementById('rotate180').onclick = () => { rotate += 180; updateTransform(); };
    document.getElementById('widthFix').onclick = () => {
        const element = previewContainer.querySelector('img, canvas');
        if (element) {
            element.style.objectFit = "scale-down";
            element.style.width = "100%";
            element.style.height = "auto";
        }
    };
    document.getElementById('heightFix').onclick = () => {
        const element = previewContainer.querySelector('img, canvas');
        if (element) {
            element.style.objectFit = "scale-down";
            element.style.width = "auto";
            element.style.height = "100%";
        }
    };
    document.getElementById('bothFix').onclick = () => {
        const element = previewContainer.querySelector('img, canvas');
        if (element) {
            element.style.objectFit = "contain";
            element.style.width = "100%";
            element.style.height = "100%";
        }
    };
    document.getElementById('toggleThumbnail').onclick = () => {
        thumbnailContainer.style.display = thumbnailContainer.style.display === 'none' ? 'block' : 'none';
    };

    // Handle Dropdown and Range for Brightness & Invert
    filterSelect.addEventListener('change', () => {
        if (filterSelect.value) {
            filterRange.style.display = 'inline';
            filterRange.value = filterSelect.value === 'invert' ? invertValue : brightnessValue;
        } else {
            filterRange.style.display = 'none';
        }
    });

    filterRange.addEventListener('input', () => {
        const value = parseInt(filterRange.value, 10);
        if (filterSelect.value === 'invert') {
            invertValue = value;
        } else if (filterSelect.value === 'brightness') {
            brightnessValue = value;
        }
        updateFilter();
    });

    window.onload = loadPreview;
</script>

</body>
</html>

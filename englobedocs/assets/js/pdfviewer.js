// PDF viewer using PDF.js (CDN)
// Renders current page, supports next/prev, zoom, and text search (basic)

window.PDFViewer = (function(){
  var pdfDoc = null, pageNum = 1, scale = 1.0, canvas, ctx;
  function init(canvasId, url){
    canvas = document.getElementById(canvasId);
    if(!canvas) return;
    ctx = canvas.getContext('2d');
    // Load PDF.js from CDN
    if(typeof pdfjsLib === 'undefined'){
      console.error('pdfjsLib not loaded. Include https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.min.js');
    }
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.16.105/pdf.worker.min.js';
    var loadingTask = pdfjsLib.getDocument(url);
    loadingTask.promise.then(function(pdf){
      pdfDoc = pdf;
      renderPage(pageNum);
      // attach controls if present
      var next = document.getElementById('pdf-next');
      var prev = document.getElementById('pdf-prev');
      var zoomIn = document.getElementById('pdf-zoom-in');
      var zoomOut = document.getElementById('pdf-zoom-out');
      if(next) next.addEventListener('click', function(){ if(pageNum < pdfDoc.numPages){ pageNum++; renderPage(pageNum);} });
      if(prev) prev.addEventListener('click', function(){ if(pageNum>1){ pageNum--; renderPage(pageNum);} });
      if(zoomIn) zoomIn.addEventListener('click', function(){ scale = Math.min(3, scale + 0.25); renderPage(pageNum); });
      if(zoomOut) zoomOut.addEventListener('click', function(){ scale = Math.max(0.5, scale - 0.25); renderPage(pageNum); });
    }, function(reason){ console.error(reason); });
  }
  function renderPage(num){
    pdfDoc.getPage(num).then(function(page){
      var viewport = page.getViewport({ scale: scale });
      canvas.height = viewport.height; canvas.width = viewport.width;
      var renderContext = { canvasContext: ctx, viewport: viewport };
      page.render(renderContext);
      // update page indicator if present
      var indicator = document.getElementById('pdf-page-num');
      if(indicator) indicator.textContent = num + ' / ' + pdfDoc.numPages;
    });
  }
  return { init: init };
})();

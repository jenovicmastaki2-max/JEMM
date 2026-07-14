// Minimal JS for micro-interactions
document.addEventListener('DOMContentLoaded', function(){
  // example micro-interaction: button ripple
  document.querySelectorAll('.btn').forEach(function(btn){
    btn.addEventListener('click', function(e){
      var el = e.currentTarget;
      el.style.transform = 'translateY(-1px)';
      setTimeout(function(){ el.style.transform = ''; },120);
    });
  });
});

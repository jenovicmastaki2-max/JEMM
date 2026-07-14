document.addEventListener('DOMContentLoaded', function(){
  // simple micro-interactions
  document.querySelectorAll('.btn').forEach(function(btn){
    btn.addEventListener('click', function(e){
      e.currentTarget.style.transform = 'translateY(-1px)';
      setTimeout(function(){ e.currentTarget.style.transform = ''; },120);
    });
  });

  // Live search
  var input = document.getElementById('search-input');
  var results = document.getElementById('search-results');
  var timeout = null;
  if(input){
    input.addEventListener('input', function(){
      clearTimeout(timeout);
      var q = this.value.trim();
      if(q.length < 2){ results.innerHTML = ''; return; }
      timeout = setTimeout(function(){
        fetch('/englobedocs/search.php?q=' + encodeURIComponent(q))
          .then(r => r.json())
          .then(data => {
            if(!data.length){ results.innerHTML = ''; return; }
            var html = '<div style="background:var(--card);backdrop-filter:blur(6px);padding:.5rem;border-radius:8px;position:absolute;left:50%;transform:translateX(-50%);width:90%;max-width:800px;z-index:40">';
            data.forEach(function(d){
              html += '<a class="search-item" href="/englobedocs/document.php?id='+d.id+'" style="display:flex;gap:.5rem;padding:.4rem;border-bottom:1px solid rgba(255,255,255,0.03);text-decoration:none;color:var(--text)">';
              if(d.cover) html += '<img src="/englobedocs/uploads/'+d.cover+'" style="width:48px;height:64px;object-fit:cover;border-radius:6px">';
              html += '<div><strong>'+escapeHtml(d.title)+'</strong><div style="opacity:.8;font-size:.9rem">'+escapeHtml(d.author || '')+' • '+escapeHtml(d.type)+'</div></div></a>';
            });
            html += '</div>';
            results.innerHTML = html;
          });
      }, 250);
    });

    document.addEventListener('click', function(e){
      if(!e.target.closest('#search-results')){ results.innerHTML = ''; }
    });
  }

  function escapeHtml(s){ return (s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
});

async function updateNotifBadge(){
  const user = JSON.parse(localStorage.getItem('usuario')) 
             || JSON.parse(sessionStorage.getItem('usuario'));
  if (!user) return;
  try {
    const res = await fetch('../BACKEND/Controlador/Notification_count.php', {
      method:'POST',
      headers:{'Content-Type':'application/x-www-form-urlencoded'},
      body:new URLSearchParams({ viewer:user.id_usuario })
    });
    const js = await res.json();
    const b = document.getElementById('notif-badge');
    if (js.status==='success' && js.unread>0){
      b.textContent = js.unread;
      b.style.display = 'inline-block';
    } else {
      b.style.display = 'none';
    }
  } catch(e){ console.error(e) }
}
document.addEventListener('DOMContentLoaded', updateNotifBadge);

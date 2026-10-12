document.addEventListener('change', function(e){if(e.target.matches('[data-delivery-type]')){const address=document.querySelector('[data-address-wrap]');if(address){address.hidden=e.target.value!=='delivery';const input=address.querySelector('textarea');if(input)input.required=e.target.value==='delivery';}}});document.addEventListener('click',function(e){const target=e.target.closest('[data-confirm]');if(target&&!confirm(target.dataset.confirm)){e.preventDefault();}});
document.addEventListener('click',function(e){
  const toggle=e.target.closest('[data-password-toggle]');
  if(!toggle) return;
  const input=document.querySelector(toggle.dataset.passwordToggle);
  if(!input) return;
  const visible=input.type==='text';
  input.type=visible?'password':'text';
  toggle.textContent=visible?'◉':'◌';
  toggle.setAttribute('aria-label',visible?'Mostrar contraseña':'Ocultar contraseña');
});

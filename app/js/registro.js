"use strict";
document.addEventListener('DOMContentLoaded', () => {
 const form=document.getElementById('formRegistro'); if(!form)return;
 const pass=document.getElementById('password'), confirm=document.getElementById('password_confirm');
 const birth=document.getElementById('fecha_nacimiento'), dui=document.getElementById('dui');
 const criteria={length:v=>v.length>=8, upper:v=>/[A-Z]/.test(v),lower:v=>/[a-z]/.test(v),number:v=>/[0-9]/.test(v),special:v=>/[^A-Za-z0-9]/.test(v)};
 function passwordFeedback(){const v=pass.value;for(const [key,test] of Object.entries(criteria)){const node=document.getElementById('rule-'+key);if(!node)continue;const ok=test(v);node.classList.toggle('text-success',ok);node.classList.toggle('text-muted',!ok);const icon=node.querySelector('i');if(icon)icon.className='bi '+(ok?'bi-check-circle-fill':'bi-circle')+' me-1';} if(confirm.value)confirm.setCustomValidity(confirm.value===v?'':'Las contraseñas no coinciden');}
 pass?.addEventListener('input',passwordFeedback); confirm?.addEventListener('input',passwordFeedback);
 document.querySelectorAll('.toggle-password').forEach(btn=>btn.addEventListener('click',()=>{const input=document.getElementById(btn.dataset.target);if(input){input.type=input.type==='password'?'text':'password';btn.setAttribute('aria-label',input.type==='password'?'Mostrar contraseña':'Ocultar contraseña');}}));
 dui?.addEventListener('input',()=>{let digits=dui.value.replace(/\D/g,'').slice(0,9);dui.value=digits.length>8?digits.slice(0,8)+'-'+digits.slice(8):digits;});
 function checkAge(){if(!birth?.value)return;const date=new Date(birth.value+'T12:00:00'),today=new Date();const last=new Date(today.getFullYear()-18,today.getMonth(),today.getDate());const ok=!Number.isNaN(date.getTime())&&date<=last;birth.setCustomValidity(ok?'':'Debes tener al menos 18 años.');const badge=document.getElementById('ageFeedbackBadge');if(badge){badge.textContent=ok?'Edad válida (18 años o más)':'Debes tener 18 años o más para registrarte';badge.className='mt-1 small '+(ok?'text-success':'text-danger');}}
 birth?.addEventListener('change',checkAge);if(birth?.value)checkAge();passwordFeedback();
 form.addEventListener('submit',event=>{passwordFeedback();checkAge();if(!form.checkValidity()){event.preventDefault();form.reportValidity();}});
});

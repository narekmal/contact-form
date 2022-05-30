
const initContactForm = () => {
  const form = document.querySelector('.js-contact-form');

  if (!form) { return; }

  const uploadBtn = form.querySelector('.js-upload-btn');
  const uploadBtnReal = form.querySelector('.js-upload-btn-real');

  uploadBtn.addEventListener('click', (e) => {
    e.preventDefault();
    uploadBtnReal.click();
  });

  $('input[name="date"]').daterangepicker({
    singleDatePicker: true
  });

  form.addEventListener('submit', (e) => {
    e.preventDefault();


    const blockElement = form.closest('.contact-form');
    blockElement.classList.remove('contact-form--success');
    blockElement.classList.remove('contact-form--failure');

    const formData = new FormData(form);

    // return;
    // const inputNames = ['name', 'email', 'contact-form-nonce'];
    // let valid = true;

    // inputNames.forEach((name) => {
    //   const { value } = form.querySelector(`[name='${name}']`);
    //   if (valid) { valid = (value !== ''); }
    //   formData.append(name, value);
    // });
    // console.log(formData);

    // if (!valid) {
    //   blockElement.classList.add('contact-form--invalid');
    //   return;
    // }

    blockElement.classList.remove('contact-form--invalid');
    blockElement.classList.add('contact-form--processing');
    formData.append('action', 'process_contact_form_data');

    fetch(ajaxUrl, {
      method: 'POST',
      body: formData,
      headers: {
        //'Content-Type': 'multipart/form-data'
      }
    })
      .then((response) => {
        response.json().then((response) => {
          blockElement.classList.remove('contact-form--processing');
          blockElement.classList.add(response.success ? 'contact-form--success' : 'contact-form--failure');
        });
      })
      .catch((response) => {
        blockElement.classList.remove('contact-form--processing');
        blockElement.classList.add('contact-form--failure');
      });
  });
};

document.addEventListener('DOMContentLoaded', () => {
  initContactForm();
});

function handleFileSelect(obj) {
  var file = obj.value;
  var fileName = file.split("\\");
  document.querySelector(".js-file-name").innerHTML = fileName[fileName.length - 1];
}
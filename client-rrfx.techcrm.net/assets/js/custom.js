// Loading Buttons
const buttonsTarget = document.querySelectorAll('form button[type="submit"]');
const observer = new MutationObserver((mutationList) => {
    for(const mutation of mutationList) {
        if(mutation.type === "attributes" && mutation.attributeName === "class") {
            const el = mutation.target;
            let isLoading = el.classList.contains('loading');
            if(isLoading) {
                el.dataset.originalText = el.textContent;
                el.textContent = "Loading...";
                el.setAttribute('disabled', 'true');
            }else {
                el.textContent = el.dataset.originalText;
                el.removeAttribute('disabled');
            }
        }
    }
})

buttonsTarget.forEach((val) => {
    observer.observe(val, {attributes: true});
})


// Required label
const requiredLabels = document.querySelectorAll('label.required');
requiredLabels.forEach((el) => {
    if(el.classList.contains('required')) {
        el.innerHTML = `${el.textContent} <span class="text-danger">*</span>`;  
    }
})
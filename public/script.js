const propsContainer = document.getElementById('props-container'),
    filter = document.getElementById('filter'),
    loading = document.querySelector('.loader')


let limit = 5;
let page = 1;

// fetch Properties from our API
async function getProperties() {
    const res = await fetch(`http://localhost:3000/index.php?_limit=${limit}&_page=${page}`)
    return await res.json();
}

// show Properties in DOM
async function showProperties() {
    const properties = await getProperties();

    properties.forEach(prop => {
        const propEl = document.createElement('div');
        propEl.classList.add('prop');
        propEl.innerHTML = `
            <div class="number">${prop.id}</div>
            <div class="prop-info">
                <h2 class="prop-town">${prop.town}</h2>
                <p class="prop-body">${prop.price}</p>
            </div>
        `;
        propsContainer.appendChild(propEl);
    })
}

// show loader and fetch more posts
function showLoading() {
    loading.classList.add('show');

    setTimeout(() => {
        loading.classList.remove('show');
        setTimeout(() => {
            page++;
            showProperties();
        }, 300)
    }, 1000);
}

// filter posts
function filterProperties(e) {
    const term = e.target.value.toUpperCase();
    const props = document.querySelectorAll('.prop');

    props.forEach(prop => {
        const title = prop.querySelector('.prop-title').innerText.toUpperCase();
        const body = prop.querySelector('.prop-body').innerText.toUpperCase();

        if (title.indexOf(term) > -1 || body.indexOf(term) > -1) {
            prop.style.display = 'flex';
        } else {
            prop.style.display = 'none';
        }
    })
}

/**
 * Init
 */

showProperties();

window.addEventListener('scroll', () => {
    const {scrollTop, scrollHeight, clientHeight} = document.documentElement;

    if (scrollTop + clientHeight >= scrollHeight - 5) {
        showLoading();
    }
});

filter.addEventListener('input', filterProperties);

const topMenu = document.querySelector('#topMenu')
const filmsList = document.querySelector('#filmList')
const loadsPlace = document.querySelector('#loadsSelect')
const throbberPlace = document.querySelector('#throbber')
const filmPopupWindow = document.querySelector('#filmPopupWindow')


const menuRender = {
    rating: Object,
    template: function (data) {
        let menuItem = document.createElement('span')
        let active = (data.id === curState.filmTypeId ? 'active' : '')
        menuItem.innerHTML = `<a href="/${curState.loadId}/${data.id}" data-id="${data.id}" class="${active}">${data.name}</a>`
        return menuItem
    },
    render: async function (rating) {
        this.rating = rating
        let self = this
        throbber.start()
        await fetch('/', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action: 'menuList'})
        })
            .then(response => response.json())
            .then(items => {
                topMenu.innerHTML = ''
                items.forEach(item => {
                    let link = self.template(item)
                    link.addEventListener('click', event => {
                        let target = event.target
                        event.preventDefault()
                        curState.filmTypeId = target.dataset.id
                        setState()
                        self.rating.render().then(() => {
                            document.querySelectorAll('#topMenu a').forEach(link => {
                                link.classList.remove('active')
                            })
                            target.classList.add('active')
                        })
                        setState()
                        return false
                    })
                    topMenu.appendChild(link)
                })
                throbber.stop()
            })
    }
}
const ratingRender = {
    templateRatingsTable: function (data) {
        let {header, items} = data
        this.table = document.createElement('table')
        this.table.appendChild(this.templateHeader(header))
        this.table.appendChild(this.templateRatings(items, header))
        return this.table
    },
    templateRatings: (items, captions) => {
        let rating = document.createElement('tbody')
        items.forEach((item) => {
            let tr = document.createElement('tr')
            for (let field in captions) {
                if (captions.hasOwnProperty(field)) {
                    let td = document.createElement('td')
                    if (field === 'name') {
                        let a = document.createElement('a')
                        a.classList.add('film-cover')
                        a.dataset.id = item.id
                        a.href = `/-${item.id}`
                        let img = document.createElement('img')
                        img.src = item.cover
                        img.alt = item.name
                        a.appendChild(img)
                        let h3 = document.createElement('h3')
                        h3.innerText = item.name
                        a.appendChild(h3)
                        a.addEventListener('click', (event) => {
                            event.preventDefault()
                            let filmId = event.target.parentNode.dataset.id
                            if (filmId) {
                                filmPopup.render(filmId)
                            }
                            return false;
                        })
                        td.appendChild(a)
                    } else {
                        td.innerHTML = item[field]
                    }
                    tr.appendChild(td)
                }
            }
            rating.appendChild(tr)
        })
        return rating
    },
    templateHeader: function (captions) {
        let self = this
        let header = document.createElement('thead')
        let tr = document.createElement('tr')
        let npp = 0
        for (let field in captions) {
            if (captions.hasOwnProperty(field)) {
                let th = document.createElement('th')
                let a = document.createElement('a')
                a.innerHTML = captions[field]
                a.dataset.id = (Math.abs(Number(curState.orderField)) === npp ? -(npp + 1) : (npp + 1)) + '';
                a.href = `/${curState.loadId}/${curState.loadId}/${a.dataset.id}`
                a.addEventListener('click', (event) => {
                    event.preventDefault()
                    curState.orderField = event.target.dataset.id = (-Number(event.target.dataset.id)) + '';
                    event.target.href = `/${curState.loadId}/${curState.loadId}/${curState.orderField}`
                    setState()
                    self.fetchData().then((data) => {
                        let {items, header} = data
                        self.table.children[1].outerHTML = ''
                        self.table.appendChild(self.templateRatings(items, header))
                        setState()
                    })
                    return false;
                })
                th.appendChild(a)
                tr.appendChild(th)
                npp++;
            }
        }
        header.appendChild(tr)
        return header
    },
    fetchData: async () => {
        throbber.start()
        return await fetch('/', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action: 'ratingList', url: document.location.pathname})
        }).then(response => {
            throbber.stop()
            return response.json()
        })
    },
    render: async function () {
        let self = this
        this.fetchData().then((data) => {
            filmsList.innerHTML = ''
            filmsList.appendChild(self.templateRatingsTable(data))
            setState()
        })
    }
}
const loadsSelect = {
    template: (item) => {
        let option = document.createElement('option')
        option.innerHTML = item.dt
        option.value = item.id
        return option
    },
    render: async function (menu, rating) {
        let self = this
        throbber.start()
        await fetch('/', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action: 'loadsSelect'})
        })
            .then(response => response.json())
            .then(data => {
                let select = document.createElement('select')
                data.forEach((item) => {
                    select.appendChild(self.template(item))
                    if ((select.options.length === 1) && (curState.loadId <= 0)) {
                        curState.loadId = select.value = item.id
                    }
                })
                select.addEventListener('change', (event) => {
                    curState.loadId = event.target.value
                    setState()
                    menu.render(rating).then(() => {
                        rating.render()
                    })
                })
                loadsPlace.appendChild(select)
                setState()
                throbber.stop()
            })
    }
}
const popup = {
    template: (data) => {
        let div = document.createElement('div')
        div.classList.add('popup-inner')
        div.innerHTML = `
<span class="close-popup">X</span>
<h2>${data.name}</h2>
<div class="film-info-wrapper">
<div class="img"><img src="${data.cover}" alt="${data.name}"></div>
<ul class="info">
<li><label>Год выхода на экран:</label> ${data.year}</li>
<li><label>Расчетный ретинг:</label> ${data.calc_ball}</li>
<li><label>Всего проголосовало:</label> ${data.votes}</li>
<li><label>Средний балл:</label> ${data.avg_ball}</li>
<li>${data.description}</li>
</ul>
</div>`
        return div;
    },
    render: function (filmId) {
        let self = this
        throbber.start()
        fetch('/', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action: 'filmInfo', url: filmId})
        })
            .then(response => {
                return response.json()
            })
            .then((data) => {
                filmPopupWindow.innerHTML = ''
                filmPopupWindow.appendChild(self.template(data))
                filmPopupWindow.classList.remove('hidden')
                document.querySelector('.close-popup').addEventListener('click', () => {
                    filmPopupWindow.classList.add('hidden')
                })
                throbber.stop()
            })
    }
}

const Throbber = {
    starts: 0,
    start: function () {
        this.starts++
        this.show()
        console.log('tStart',this.starts)
    },
    show: function () {
        let self = this
        setTimeout(function () {
            if (self.starts > 0) {
                console.log('tShow',self.starts)
                throbberPlace.classList.remove('hidden')
            }
        }, 1000)

    },
    stop: function () {
        this.starts--;
        throbberPlace.classList.add('hidden')
        console.log('tStop',this.starts)
    }
}

const setState = () => {
    let suffix = ''
    let selMenu = document.querySelector('#topMenu a.active')
    if (selMenu !== null) {
        document.title = selMenu.innerText;
    }
    let sel = document.querySelector('#loadsSelect select')
    if (sel != null) {
        for (let i in sel.options) {
            let option = sel.options[i]
            if (option.value === curState.loadId) {
                suffix = ' - ' + option.innerText;
                option.setAttribute('selected', true)
            }
        }
    }
    document.querySelectorAll('#filmList th a').forEach((item) => {
        item.classList.remove('order')
        item.classList.remove('desc')
        if (Math.abs(item.dataset.id) === Math.abs(curState.orderField)) {
            item.classList.add('order')
            if (curState.orderField < 0) {
                item.classList.add('desc')
            }
        }
    })
    document.title += suffix
    history.pushState(curState, document.title, `/${curState.loadId}/${curState.filmTypeId}/${curState.orderField}`)
}


let rating = Object.create(ratingRender)
let menu = Object.create(menuRender)
let loads = Object.create(loadsSelect)
let filmPopup = Object.create(popup)
let throbber = Object.create(Throbber)

let curState = {loadId: 0, filmTypeId: 1, orderField: 1}
if (window.location.pathname.length > 1) {
    let params = window.location.pathname.split('/')
    if (params[1] !== undefined) {
        curState.loadId = params[1]
    }
    if (params[2] !== undefined) {
        curState.filmTypeId = params[2]
    }
    if (params[3] !== undefined) {
        curState.orderField = params[3]
    }
}

loads.render(menu, rating).then(() => {
    menu.render(rating).then(() => {
        rating.render().then(() => {
            setState()
        })
    })
})

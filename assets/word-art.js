const topMenu = document.querySelector('#topMenu')
const films = document.querySelector('#filmList')
const loads = document.querySelector('#loadsList')
const menuRender = {
    rating: Object,
    menuItemTemplate: (data) => {
        let menuItem = document.createElement('span')
        menuItem.innerHTML = `<a href="/${data.id}">${data.name}</a>`
        return menuItem
    },
    renderMenu: function (rating) {
        this.rating = rating
        let self = this
        fetch('/', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action: 'menuList'})
        })
            .then(response => response.json())
            .then(items => {
                items.forEach(item => {
                    let link = self.menuItemTemplate(item)
                    link.addEventListener('click', event => {
                        let target = event.target
                        event.preventDefault()
                        history.pushState({url: target.href}, target.innerText, target.href)
                        self.rating.renderRating().then(() => {
                            document.querySelectorAll('#topMenu a').forEach(link => {
                                link.classList.remove('active')
                            })
                            target.classList.add('active')
                            document.title = target.innerText
                        })
                        return false
                    })
                    topMenu.appendChild(link)
                })
            })
    }
}
const ratingRender = {
    templateRatingsTable: function (data) {
        let {header, items} = data
        let table = document.createElement('table')
        table.appendChild(this.templateHeader(header))
        table.appendChild(this.templateRatings(items, header))
        return table
    },
    templateRatings: (items, captions) => {
        let rating = document.createElement('tbody')
        let html = ''
        items.forEach((item) => {
            html += '<tr>'
            for (let field in captions) {
                if (captions.hasOwnProperty(field)) {
                    if (field === 'name') {
                        html += `<td><a href="/film/${item.id}">${item.name}</a></td>`
                    } else {
                        html += `<td>${item[field]}</td>`
                    }
                }
            }
            html += '</tr>'
        })

        rating.innerHTML = html
        return rating
    },
    templateHeader: (captions) => {
        let header = document.createElement('thead')
        let html = '<tr>'
        for (let field in captions) {
            if (captions.hasOwnProperty(field)) {
                html += `<th><a href="#">${captions[field]}</a></th>`
            }
        }
        header.innerHTML = html
        return header
    },
    renderRating: async function () {
        let self = this
        await fetch('/', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({action: 'ratingList', params: {url: document.location.pathname}})
        })
            .then(response => response.json())
            .then(data => {
                films.innerHTML = ''
                films.appendChild(self.templateRatingsTable(data))
            })
    }
}

let rating = Object.create(ratingRender)
let menu = Object.create(menuRender)
menu.renderMenu(rating)

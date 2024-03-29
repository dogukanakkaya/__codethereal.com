//import 'bootstrap'
import 'datatables.net'
import ceToast from './ce/toast'
import { STORAGE_PREFIX } from "./constants"
import './common'

window.Popper = require('popper.js').default;
window.$ = window.jQuery = require('jquery');
window.bootstrap = require('bootstrap')

window.makeToast = ceToast

const availableThemeColors = ['light-theme', 'dark-theme']
const availableSidebarStyles = ['with-bg', 'without-bg']

const withZeros = time => time < 10 ? "0" + time : time;

window.addEventListener('DOMContentLoaded', () => {
    // If user is in tablet just close menu at start
    const mediaQuery = window.matchMedia('(max-width: 767px)')
    if (mediaQuery.matches) {
        document.getElementById('menu-toggle').removeAttribute('checked')
    }

    const liveTimeEl = document.querySelector('.live-time span');
    setInterval(() => {
        const d = new Date();
        liveTimeEl.textContent = withZeros(d.getHours()) + ":" + withZeros(d.getMinutes()) + ":" + withZeros(d.getSeconds());
    }, 1000);
})

window.addEventListener('load', () => {
    // Remove loader
    document.getElementById('loader').remove()

    // Theme color, dark or light mode
    const themeColor = localStorage.getItem(`${STORAGE_PREFIX}_theme_color`) || availableThemeColors[0]

    // Sidebar style, with bg or without bg
    const sidebarStyle = localStorage.getItem(`${STORAGE_PREFIX}_sidebar_style`) || availableSidebarStyles[0]

    document.getElementById(themeColor).setAttribute('checked', true)
    document.getElementById(sidebarStyle).setAttribute('checked', true)
    toggleThemeColor(themeColor)
    toggleSidebarStyle(sidebarStyle)
})

window.toggleThemeColor = (theme = null) => {
    const activeTheme = localStorage.getItem(`${STORAGE_PREFIX}_theme_color`)
    if (!theme){
        // If user clicks on night icon to toggle theme color
        // Find first except activeTheme and toggle it
        const firstExceptActiveTheme = availableThemeColors.find(themeColor => themeColor !== activeTheme)
        toggleThemeColor(firstExceptActiveTheme)
    }else{
        localStorage.setItem(`${STORAGE_PREFIX}_theme_color`, theme)
        document.querySelector('html').setAttribute('theme', theme)
    }
}

window.toggleSidebarStyle = (style = null) => {
    document.querySelector('aside').setAttribute('theme', style)
    localStorage.setItem(`${STORAGE_PREFIX}_sidebar_style`, style)
}

// Open the settings sidebar (right)
window.toggleThemeSettings = () => {
    document.querySelector('.settings-sidebar').classList.toggle('r-0')
    document.querySelector('.black-overlay').classList.toggle('d-block')
}

// Find submit types and toggle them loading
window.toggleBtnLoading = () => {
    document.querySelector('[type=submit]').toggleAttribute('disabled')
    document.querySelector('[type=submit] .btn-enabled').classList.toggle('d-none')
    document.querySelector('[type=submit] .btn-disabled').classList.toggle('d-none')
}

/**
 * Serialize nested sortable
 *
 * @param sortable
 * @param sortableGroup
 * @returns {[]}
 */
window.nestedSortableSerialize = (sortable, sortableGroup) => {
    const serialized = [];
    const children = [].slice.call(sortable.children);
    for (let i in children) {
        const nested = children[i].querySelector(sortableGroup)

        // Find the closest sortable group and get it's data-parent-id attribute
        const parentId = children[i].closest(sortableGroup).getAttribute('data-parent-id')
        serialized.push({
            id: children[i].dataset['sortableId'],
            parent_id: parentId
        });
        if (nested){
            serialized.push(...nestedSortableSerialize(nested, sortableGroup))
        }
    }
    return serialized
}

// Axios instance of my own
request.interceptors.response.use((response) => response, (error) => {
    makeToast({
        status: 0,
        title: 'Error',
        message: error.message
    })
    toggleBtnLoading()
})

window.bottomAlert = ({ text, timeout = null }) => {
    const quickAlert = document.querySelector('.quick-alert')
    quickAlert.classList.add('show')
    quickAlert.querySelector('.text p').textContent = text
    if (timeout){
        setTimeout(() => {
            quickAlert.classList.remove('show')
        }, timeout)
    }
}

document.querySelectorAll('.has-dd').forEach(dd => {
    dd.addEventListener('click', () => {
        dd.querySelector('.menu-dd').classList.toggle('show')
    })
})

/* Draggable setup */
const draggables = document.querySelectorAll('.draggable');
let initX, initY, mousePressX, mousePressY;

draggables.forEach(draggable => {
    draggable.addEventListener('mousedown', function (e){
        initX = this.offsetLeft;
        initY = this.offsetTop;
        mousePressX = e.clientX;
        mousePressY = e.clientY;
        this.addEventListener('mousemove', repositionElement, false);

        window.addEventListener('mouseup', () => {
            draggable.removeEventListener('mousemove', repositionElement, false);
        }, false);
    })
})
function repositionElement(e) {
    this.style.left = initX + e.clientX - mousePressX + 'px';
    this.style.top = initY + e.clientY - mousePressY + 'px';
}
/* /Draggable setup */

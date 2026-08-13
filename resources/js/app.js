import { createApp, h } from 'vue'
import { createInertiaApp, Link, Head } from '@inertiajs/vue3'
import { createPinia } from 'pinia'
const resolvePageComponent = async (name, pages) => {
  const path = `./Pages/${name}.vue`
  if (path in pages) {
    const module = await pages[path]()
    return module.default
  }
  throw new Error(`Page not found: ${path}`)
}
import '../css/app.css'

const pinia = createPinia()

createInertiaApp({
  title: (title) => `${title} - NM System`,
  resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    const app = createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(pinia)
      .component('Link', Link)
      .component('Head', Head)
    app.mount(el)
  },
})

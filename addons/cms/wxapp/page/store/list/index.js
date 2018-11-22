const app = getApp()

Page({
    data: {
        cdn_url: '',
        store_layout: '',
        list: [],
        activeIndex: 0,
    },
    onLoad() {
        this.getList()
    },
    getList() {
        app.request('/store/store_show', {}, (data, ret) => {
            console.log(data)

            let list = []

            Object.keys(data.list).forEach((n) => {
                list = [...list, ...data.list[n]]
            })

            this.setData({
                cdn_url: data.cdn_url,
                store_layout: data.store_layout,
                list,
                store: list,
            })
        }, (data, ret) => {
            console.log(data)
            app.error(ret.msg)
        })
    },
    changeTab(e) {
        const { index } = e.currentTarget.dataset

        this.setData({
            activeIndex: index,
        })
    },
    onChange(e) {
        console.log(e)
        const { value } = e.detail
        const list = this.data.store.filter((n) => n.cities_name.indexOf(value.trim()) !== -1)

        this.setData({
            list,
        })
    },
})
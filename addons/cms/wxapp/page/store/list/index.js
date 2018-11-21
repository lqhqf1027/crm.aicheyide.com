const app = getApp()

Page({
    data: {
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
            this.setData({
                store_layout: data.store_layout,
                list: data.list,
                store: data.list,
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
const app = getApp()

Page({
    data: {
        new: [],
        used: [],
        logistics: [],
        inputVal: '',
    },
    onCancel() {
        wx.navigateBack()
    },
    onChange(e) {
        console.log('onChange', e)
        this.setData({ inputVal: e.detail.value })
        this.getList(e.detail.value)
    },
    onConfirm(e) {
        console.log('onConfirm', e)
        this.switchTab(e.detail.value)
    },
    getList(queryModels) {
        const city = wx.getStorageSync('city')

        if (this.timeout) {
            clearTimeout(this.timeout)
            this.timeout = null
        }

        if (!queryModels || !city) {
            this.setData({
                new: [],
                used: []
            })
            return
        }

        this.timeout = setTimeout(() => {
            app.request('/share/searchModels?noAuth=1', { city_id: city.id, queryModels }, (data, ret) => {
                console.log(data)
                this.setData({
                    new: data && data.searchModel.new,
                    used: data && data.searchModel.used,
                    logistics: data && data.searchModel.logistics,
                })
            }, (data, ret) => {
                console.log(data)
                app.error(ret.msg)
            })
        }, 250)
    },
    onClick(e) {
        console.log(e)
        const { value } = e.currentTarget.dataset
        const { name, style, type } = value
        this.switchTab(name, type || style)
    },
    switchTab(name = '', style = 'new') {
        wx.setStorage({
            key: 'searchVal',
            data: {
                name,
                style,
            },
            success() {
                wx.switchTab({
                    url: '/page/index/index',
                })
            },
        })
    },
})
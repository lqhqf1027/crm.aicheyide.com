const app = getApp()

Page({
    data: {
        new: [],
        used: [],
    },
    onCancel() {
        wx.switchTab({
            url: '/page/preference/list/index',
        })
    },
    onChange(e) {
        console.log('onChange', e)
        this.getList(e.detail.value)
    },
    getList(queryModels) {
        if (this.timeout) {
            clearTimeout(this.timeout)
            this.timeout = null
        }

        if (!queryModels) {
            this.setData({
                new: [],
                used: []
            })
            return
        }

        this.timeout = setTimeout(() => {
            app.request('/share/searchModels', { queryModels }, (data, ret) => {
                console.log(data)
                this.setData({
                    new: data && data.new,
                    used: data && data.used,
                })
            }, (data, ret) => {
                console.log(data)
                    // app.error(ret.msg)
            })
        }, 250)
    },
})
const app = getApp()

Page({
    data: {
        key: 'fabulous',
        index: 0,
    },
	onShow() {
		this.getList()
	},
    onPullDownRefresh() {
        this.getList()
    },
    onRefresh() {
        this.getList()
    },
	getList() {
        app.request('/my/coupons', {}, (data, ret) => {
            console.log(data)
            wx.stopPullDownRefresh()
        }, (data, ret) => {
            console.log(data)
            app.error(ret.msg)
        })
    },
    onChange(e) {
        const { key } = e.detail
        const index = this.data.integral.map((n) => n.type).indexOf(key)

        this.setData({
            key,
            index,
        })
    },
    onSwiperChange(e) {
        const { current: index, source } = e.detail
        const { type: key } = this.data.integral[index]

        if (!!source) {
            this.setData({
                key,
                index,
            })
        }
    },
})
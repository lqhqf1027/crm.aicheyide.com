const app = getApp()

Page({
    data: {
        currentScore: 0,
        integral: [],
        key: 'fabulous',
        index: 0,
    },
	onLoad() {
		this.getList()
	},
    onPullDownRefresh() {
        this.getList()
    },
	getList() {
        const user_id = app.globalData.userInfo.id

        app.request('/my/myScore', { user_id }, (data, ret) => {
            console.log(data)
            this.setData({
                integral: data && data.integral,
                currentScore: data && data.currentScore,
            })
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
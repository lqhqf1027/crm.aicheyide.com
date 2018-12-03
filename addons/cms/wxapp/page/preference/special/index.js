const app = getApp()

Page({
    data: {
    	globalData: app.globalData,
        special: {},
    },
    onLoad(options) {
        console.log(options)
        this.options = options
        this.setData({ globalData: app.globalData })
        this.getDetail()
    },
    onReady() {
    	if (!this.options.title) return

		wx.setNavigationBarTitle({
			title: decodeURIComponent(this.options.title),
		})
    },
    getDetail() {
        const special_id = this.options.id

      	app.request('/index/specialDetails', { special_id }, (data, ret) => {
            console.log(data)
            this.setData({
            	special: data,
            })
        }, (data, ret) => {
            console.log(data)
            app.error(ret.msg)
        })
    },
    onOpenDetail(e) {
        const { id, type } = e.currentTarget.dataset

        wx.navigateTo({
            url: `/page/preference/detail/index?id=${id}&type=${type}`,
        })
    },
})
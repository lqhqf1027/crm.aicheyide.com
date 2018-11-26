const app = getApp()

Page({
    data: {
        plan: {},
        guesslike: [],
        vehicle_configuration: {},
    },
    onLoad(options) {
        console.log(options)
        this.options = options
        this.getDetail()
    },
    getDetail() {
        const plan_id = this.options.id
        const cartype = this.options.type

        app.request('/share/plan_details', { plan_id, cartype }, (data, ret) => {
            console.log(data)
            this.setData({
                plan: data && data.plan,
                guesslike: data && data.guesslike,
                vehicle_configuration: data && data.plan.models.vehicle_configuration,
            })
        }, (data, ret) => {
            console.log(data)
            app.error(ret.msg)
        })
    },
    openLocation() {
        wx.openLocation({
            latitude: 39.90,
            longitude: 116.40,
        })
    },
    openConfig() {
        const { id, type } = this.options

        wx.navigateTo({
            url: `/page/preference/config/index?id=${id}&type=${type}`,
        })
    },
    openPlan() {
        const { id, type } = this.options

        wx.navigateTo({
            url: `/page/preference/plan/index?id=${id}&type=${type}`,
        })
    },
    onPlanImageLoad(e) {
        console.log(e)
        const { width, height } = e.detail
        const planImageStyle = `width: 100%; height: ${height}rpx`

        this.setData({
            planImageStyle,
        })
    },
    onOpenDetail(e) {
        const { id, type } = e.currentTarget.dataset

        wx.navigateTo({
            url: `/page/preference/detail/index?id=${id}&type=${type}`,
        })
    },
    collectionInterface() {
        if (this.data.plan.collection === 1) {
            return app.info('已收藏')
        }

        const plan_id = this.options.id
        const cartype = this.options.type

        app.request('/share/collectionInterface', { plan_id, cartype }, (data, ret) => {
            console.log(data)
            app.success(ret.msg)
        }, (data, ret) => {
            console.log(data)
            app.error(ret.msg)
        })
    },
    fabulousInterface() {
        if (this.data.plan.fabulous === 1) {
            return app.info('已点赞')
        }

        const plan_id = this.options.id
        const cartype = this.options.type

        app.request('/share/fabulousInterface', { plan_id, cartype }, (data, ret) => {
            console.log(data)
            app.success(ret.msg)
            setTimeout(() => app.integral('fabulous'))
        }, (data, ret) => {
            console.log(data)
            app.error(ret.msg)
        })
    },
    clickAppointment() {
        if (this.data.plan.appointment === 1) {
            return app.info('已预约')
        }

        const plan_id = this.options.id
        const cartype = this.options.type

        app.request('/share/clickAppointment', { plan_id, cartype }, (data, ret) => {
            console.log(data)
            app.success(ret.msg)
        }, (data, ret) => {
            console.log(data)
            app.error(ret.msg)
        })
    },
})
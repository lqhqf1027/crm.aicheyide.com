const app = getApp()
const dateDiff = (start = '', end = '') => {
    let str = ''
    const a = new Date(start).valueOf()
    const b = new Date().valueOf()
    const c = new Date(b - a)
    const y = c.getFullYear() - 1970
    const m = c.getMonth()
    const d = c.getDate() - 1 || 1

    if (y > 0) {
        str = `${str}${y}年`
    }

    if (m) {
        str = `${str}${m}个月`
    }

    if (y <= 0 && !m) {
        return `${d}天`
    }

    return str
}

Page({
    data: {
        plan: {},
        guesslike: [],
        vehicle_configuration: {},
    },
    onLoad(options) {
        console.log(options)
        this.options = options
        this.setData({ type: options.type })
        this.getDetail()
    },
    getDetail() {
        const plan_id = this.options.id
        const cartype = this.options.type

        app.request('/share/plan_details', { plan_id, cartype }, (data, ret) => {
            console.log(data)
            this.setData({
                plan: data && data.plan,
                guesslike: data && data.guesslike || [],
                vehicle_configuration: data && data.plan.models.vehicle_configuration,
                'plan.diff_date': dateDiff(data && data.plan.car_licensedate),
            })
        }, (data, ret) => {
            console.log(data)
            app.error(ret.msg)
        })
    },
    openLocation() {
        const { latitude, longitude } = this.data.plan.companystore || {}

        if (!latitude || !longitude) return

        wx.openLocation({
            latitude: Number(latitude),
            longitude: Number(longitude),
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
            this.setData({ 'plan.collection': 1 })
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
            this.setData({ 'plan.fabulous': 1 })
            app.success(ret.msg)
            // setTimeout(() => app.integral('fabulous'))
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
            this.setData({ 'plan.appointment': 1 })
            app.success(ret.msg)
        }, (data, ret) => {
            console.log(data)
            app.error(ret.msg)
        })
    },
    alert1() {
        wx.showModal({
            title: '排放标准',
            content: '排放标准以各地车管所实际认定为准，外迁标准以迁入地车管所规定为准',
            showCancel: false,
        })
    },
    alert2() {
        wx.showModal({
            title: '过户记录',
            content: '具体结果以车辆等级证书为准',
            showCancel: false,
        })
    },
    makePhoneCall() {
        const { phone } = this.data.plan.companystore || {}

        if (!phone) return

        wx.makePhoneCall({
            phoneNumber: phone,
        })
    },
})
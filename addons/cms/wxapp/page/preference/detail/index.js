const app = getApp()
const isTel = (value) => /^1[3456789]\d{9}$/.test(value)
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
        planImageStyle: {},
        backtop: false,
        popupVisible: false,
        codeText: '获取验证码',
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
        const { type } = e.currentTarget.dataset
        const { width, height } = e.detail
        const planImageStyle = `width: 100%; height: ${height}rpx`

        this.setData({
            [`planImageStyle.${type}`]: planImageStyle,
        })
    },
    onOpenDetail(e) {
        const { id, type } = e.currentTarget.dataset

        wx.navigateTo({
            url: `/page/preference/detail/index?id=${id}&type=${type}`,
        })
    },
    collectionInterface() {
        // if (this.data.plan.collection === 1) {
        //     return app.info('已收藏')
        // }

        const plan_id = this.options.id
        const cartype = this.options.type
        const identification = this.data.plan.collection === 1 ? 1 : 0

        app.request('/share/collectionInterface', { plan_id, cartype, identification }, (data, ret) => {
            console.log(data)
            this.setData({ 'plan.collection': identification === 1 ? 0 : 1 })
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
    getPhoneNumber(e) {
        console.log(e)

        // 取消操作
        if (e.detail.errMsg !== 'getPhoneNumber:ok') {
            this.setData({ popupVisible: true })
            return
        }
    },
    clickAppointment() {
        const hasMobile = !!this.data.plan.users.mobile
        const { code, mobile } = this.data

        // 判断是否已预约
        if (this.data.plan.appointment === 1) {
            return app.info('已预约')
        }

        // 验证手机号码
        if (!hasMobile) {
            if (!mobile) {
                wx.showToast({ title: '请输入手机号码', icon: 'none' })
                return
            } else if (!isTel(mobile)) {
                wx.showToast({ title: '请输入 11 位手机号码', icon: 'none' })
                return
            } else if (!code) {
                wx.showToast({ title: '请输入验证码', icon: 'none' })
                return
            }
        }

        const plan_id = this.options.id
        const cartype = this.options.type
        const params = {
            plan_id,
            cartype,
        }

        // 设置参数
        if (hasMobile) {
            params.mobile = this.data.plan.users.mobile
        } else {
            params.mobile = mobile
            params.code = code
        }

        // 发起请求
        app.request('/share/clickAppointment', params, (data, ret) => {
            console.log(data)
            this.setData({
                'plan.appointment': 1,
                'plan.users.mobile': params.mobile,
                popupVisible: false,
            })
            app.success(ret.msg)
        }, (data, ret) => {
            console.log(data)
            app.error(ret.msg)
        })
    },
    onPopupClose() {
        this.setData({ popupVisible: false })
    },
    onChange(e) {
        const { model } = e.currentTarget.dataset

        this.setData({ [model]: e.detail.value })
    },
    /**
     * 发送验证码
     * @param {number} mobile 手机号
     */
    sendSMS(mobile) {
        app.request('/share/sendMessage', { mobile }, (data, ret) => {
            console.log(data)
            app.success(ret.msg)
        }, (data, ret) => {
            console.log(data)
            app.error(ret.msg)
        })
    },
    /**
     * 获取验证码
     */
    getCode() {
        if (this.disabled || this.timeout)  return

        // 验证手机号码
        if (!this.data.mobile) {
            wx.showToast({ title: '请输入手机号码', icon: 'none' })
            return
        } else if (!isTel(this.data.mobile)) {
            wx.showToast({ title: '请输入 11 位手机号码', icon: 'none' })
            return
        }

        // 倒计时
        this.renderCodeText()

        // 发送验证码
        this.sendSMS(this.data.mobile)
    },
    /**
     * 60s倒计时
     * @param {number} [num=60] 倒计时时间
     */
    renderCodeText(num = 60) {
        this.disabled = num !== 0

        if (num <= 0) {
            clearTimeout(this.timeout)
            this.timeout = null
            this.setData({ codeText: '重新发送' })
            return
        }

        this.setData({ codeText: `${num--} 秒` })

        this.timeout = setTimeout(() => {
            this.renderCodeText(num)
        }, 1000)
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
    onPageScroll(e) {
        this.setData({
            backtop : e.scrollTop > 100,
        })
    },
    backtop() {
        this.setData({ backtop: false })
        wx.pageScrollTo({
            scrollTop: 0,
            duration: 300,
        })
    },
    goHome() {
        wx.switchTab({
            url: '/page/preference/list/index',
        })
    },
    toStore() {
        const { id } = this.data.plan.companystore || {}

        if (!id) return

        wx.navigateTo({
            url: `/page/store/detail/index?id=${id}`,
        })
    },
    onShareAppMessage() {},
})
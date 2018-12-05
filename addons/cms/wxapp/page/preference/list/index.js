var app = getApp()

Page({
    data: {
        tags: [{
            label: '1万以内',
            payment: [0, 10],
        }, {
            label: '1-2万',
            payment: [10, 20],
        }, {
            label: '2-3万',
            payment: [20, 30],
        }, {
            label: '4万以上',
            payment: [40],
        }],
        swiperIndex: 'index',
        globalData: {},
        shares: {},
        brandPlan: {},
        brand_id: '',
        city: app.globalData.city,
        deviceHeight: '100%',
    },
    channel: 0,
    page: 1,
    onPageScroll(e) {
        this.setData({
            fixed: e && e.scrollTop,
        })
    },
    onLoad: function() {
        this.getSystemInfo()
        wx.setStorageSync('city', app.globalData.city)
    },
    onShareAppMessage: function() {
        var shares = this.data.shares || {}

        return {
            title: shares.index_share_title,
            path: '/page/index/index',
            imageUrl: shares.index_share_img,
        }
    },
    onShow: function() {
        this.setGlobalData(this.getList)
    },
    setGlobalData(cb) {
        var that = this;
        var callback = function() {
            that.setData({
                globalData: app.globalData,
            })
            typeof cb == "function" && cb(app.globalData)
        }

        if (app.globalData.userInfo) {
            callback()
            return
        }

        app.request('/common/init', {}, function(data, ret) {
            app.globalData.config = data.config; 
            app.globalData.bannerList = data.bannerList;
          console.log(data.bannerList);
            //如果需要一进入小程序就要求授权登录,可在这里发起调用
            // app.check(function(ret) {
            //     callback()
            // });
        }, function(data, ret) {
            app.error(ret.msg);
        });
    },
    bindchange(e) {
        console.log(e)
    },
    onClose() {
        this.setData({
            visible: false,
        })
    },
    onBrandPlan(e) {
        const { id } = e.currentTarget.dataset
        const { brandPlan } = this.data

        console.log(id)

        var that = this
        var city = wx.getStorageSync('city')

        // 判断是否同一城市下，取其缓存
        if (city && city.id === this.data.city.id) {
            if (brandPlan[id]) {
                this.setData({
                    visible: true,
                    brand_id: id,
                })
                return
            }
        }

        that.setData({
            city,
        })

        app.request('/index/brandPlan', { city_id: city.id, brand_id: id }, function(data, ret) {
            console.log(data)
            that.setData({
                visible: true,
                brand_id: id,
                [`brandPlan.${id}`]: data || [],
            })
        }, function(data, ret) {
            console.log(data)
            app.error(ret.msg)
        })
    },
    onSpecial(e) {
        const { id, title } = e.currentTarget.dataset

        wx.navigateTo({
            url: `/page/preference/special/index?id=${id}&title=${title}`,
        })
    },
    onOpenDetail(e) {
        const { id, type } = e.currentTarget.dataset

        wx.navigateTo({
            url: `/page/preference/detail/index?id=${id}&type=${type}`,
        })
    },
    onTag(e) {
        const { payment } = e.currentTarget.dataset

        console.log('onTag', payment)

        wx.setStorage({
            key: 'searchVal',
            data: {
                payment,
            },
            success: () => {
                this.toMore()
            },
        })
    },
    toMore() {
        wx.switchTab({
            url: '/page/index/index',
        })
    },
    onSearch() {
        wx.navigateTo({
            url: '/page/search/index',
        })
    },
    onSelect() {
        wx.navigateTo({
            url: '/page/city/index',
        })
    },
    makePhoneCall() {
        wx.makePhoneCall({
            phoneNumber: '4001886061'
        })
    },
    getSystemInfo() {
        wx.getSystemInfo({
            success: (res) => {
                this.setData({
                    deviceHeight: res.windowHeight,
                })
            }
        })
    },
    getList() {
        var that = this
        var city = wx.getStorageSync('city')

        this.setData({
            city,
        })

        app.request('/index/index', { city_id: city.id }, function(data, ret) {
            console.log(data)
            that.setData({
                appointment: data.appointment.map((n) => {
                    const mobile = n.mobile.replace(/(\d{3})\d{4}(\d{4})/, '$1****$2')
                    const content = `${mobile} 成功下单 ${n.models_name}`

                    return {...n, content }
                }),
                brandList: data.brandList,
                carType: data.carType,
                shares: data.shares,
            })
        }, function(data, ret) {
            console.log(data)
            app.error(ret.msg)
        })
    },
})
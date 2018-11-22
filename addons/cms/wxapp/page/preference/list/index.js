var app = getApp()
var city = {
    cities_name: '成都',
    id: 38,
}

Page({
    data: {
        tags: [{
            name: '1万以内',
        }, {
            name: '1-2万',
        }, {
            name: '2-3万',
        }, {
            name: '4万以上',
        }],
        swiperIndex: 'index',
        globalData: {},
        shares: {},
        city,
    },
    channel: 0,
    page: 1,
    onPageScroll(e) {
        this.setData({
            fixed: e && e.scrollTop,
        })
    },
    onLoad: function() {
        wx.setStorageSync('city', city)
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
            app.globalData.indexTabList = data.indexTabList;
            app.globalData.newsTabList = data.newsTabList;
            app.globalData.productTabList = data.productTabList;
            app.globalData.bannerList = data.bannerList;

            //如果需要一进入小程序就要求授权登录,可在这里发起调用
            app.check(function(ret) {
                callback()
            });
        }, function(data, ret) {
            app.error(ret.msg);
        });
    },
    bindchange(e) {
        console.log(e)
    },
    onOpenDetail(e) {
        const { id } = e.currentTarget.dataset

        wx.navigateTo({
            url: `/page/preference/detail/index?id=${id}`,
        })
    },
    onTag(e) {
        console.log('onTag', e)
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
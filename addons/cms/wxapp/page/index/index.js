var app = getApp()

Page({
    data: {
        globalData: {},
        shares: {},
        city: app.globalData.city,
    },
    channel: 0,
    page: 1,
    onPageScroll(e) {
        this.setData({
            fixed: e && e.scrollTop,
        })
    },
    onLoad: function() {
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
        this.getList()
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
        const city = wx.getStorageSync('city')

        this.setData({
            city,
        })

        app.request('/carselection/index', { city_id: city.id }, (data, ret) => {
            console.log(data)
        }, (data, ret) => {
            console.log(data)
            app.error(ret.msg)
        })
    },
})
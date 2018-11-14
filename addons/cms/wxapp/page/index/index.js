var app = getApp()
var city = {
    cities_name: '成都',
    id: 38,
}

Page({
    data: {
        imgUrls: [
            '/assets/images/avatar.png',
            '/assets/images/avatar.png',
            '/assets/images/avatar.png',
            '/assets/images/avatar.png'
        ],
        swiperIndex: 'index',
        globalData: {},
        city,
    },
    channel: 0,
    page: 1,
    onLoad: function() {
        wx.setStorageSync('city', city)
    },
    onShareAppMessage: function() {
        return {
            title: 'FastAdmin',
            desc: '基于ThinkPHP5和Bootstrap的极速后台框架',
            path: '/page/index/index'
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
    bindchange(e) {},
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
        var city = wx.getStorageSync('city')

        this.setData({
            city,
        })

        app.request('/index/index', { city_id: city.id }, function(data, ret) {
            console.log(data)
        }, function(data, ret) {
            console.log(data)
            app.error(ret.msg)
        })
    },
})
var app = getApp();
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
        city: 1
    },
    channel: 0,
    page: 1,
    onLoad: function() {
        // this.getList()
        // var that = this;
        // this.channel = 0;
        // this.page = 1;
        // this.setData({
        //     ["tab.list"]: app.globalData.indexTabList
        // });
        // app.request('/index/plan_details', {
        //     plan_id: 149,
        //     city_id: 1,
        //     user_id: 5
        // }, function(data, ret) {
        //     console.log(data);

        // }, function(data, ret) {
        //     app.error(ret.msg);
        // });


        // app.request('/index/index', {
        //   city:this.data.city
        // }, function (data, ret) {
        //   console.log(data);

        // }, function (data, ret) {
        //   app.error(ret.msg);
        // });

    },
    // onPullDownRefresh: function() {
    //     this.setData({ nodata: false, nomore: false });
    //     this.page = 1;
    //     this.loadArchives(function() {
    //         wx.stopPullDownRefresh();
    //     });
    // },
    // onReachBottom: function() {
    //     var that = this;
    //     this.loadArchives(function(data) {
    //         if (data.archivesList.length == 0) {
    //             app.info("暂无更多数据");
    //         }
    //     });
    // },
    // loadArchives: function(cb) {
    //     var that = this;
    //     if (that.data.nomore == true || that.data.loading == true) {
    //         return;
    //     }
    //     this.setData({ loading: true });
    //     app.request('/archives/index', { channel: this.channel, page: this.page }, function(data, ret) {
    //         that.setData({
    //             loading: false,
    //             nodata: that.page == 1 && data.archivesList.length == 0 ? true : false,
    //             nomore: that.page > 1 && data.archivesList.length == 0 ? true : false,
    //             archivesList: that.page > 1 ? that.data.archivesList.concat(data.archivesList) : data.archivesList,
    //         });
    //         that.page++;
    //         typeof cb == 'function' && cb(data);
    //     }, function(data, ret) {
    //         app.error(ret.msg);
    //     });
    // },
    // handleZanTabChange(e) {
    //     var componentId = e.componentId;
    //     var selectedId = e.selectedId;
    //     this.channel = selectedId;
    //     this.page = 1;
    //     this.setData({
    //         nodata: false,
    //         nomore: false,
    //         [`${componentId}.selectedId`]: selectedId
    //     });
    //     wx.pageScrollTo({ scrollTop: 0 });
    //     this.loadArchives();
    // },
    onShareAppMessage: function() {
        return {
            title: 'FastAdmin',
            desc: '基于ThinkPHP5和Bootstrap的极速后台框架',
            path: '/page/index/index'
        }
    },
    onShow: function() {
        this.setGlobalData(this.getList)
            // var that = this;
            // app.request('/text/index', {}, function(data, ret) {
            //     that.setData({
            //         text: data.plan,
            //     });
            //     console.log(data.plan);
            // }, function(data, ret) {
            //     app.error(ret.msg);
            // });
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
    makePhoneCall() {
        wx.makePhoneCall({
            phoneNumber: '4001886061'
        })
    },
    getList() {
        app.request('/index/index', { city: '1' }, function(data, ret) {
            console.log(data)
        }, function(data, ret) {
            console.log(data)
            app.error(ret.msg)
        })
    },
})
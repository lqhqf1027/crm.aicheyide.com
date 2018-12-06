var app = getApp();
Page({
    data: {
        globalData: app.globalData,
        isWxapp: true,
        userInfo: {
            id: 0,
            avatar: '/assets/images/avatar.png',
            nickname: '游客',
            balance: 0,
            score: 0,
            level: 0
        },
        actions: [
            {
                name: '取消收藏',
                width: 100,
                color: '#fff',
                background: '#ff6740',
            },
        ],
        toggle: false,
        pickup: {},
        collection: {},
        subscribe: {},
        couponCount: 0,
        score: 0,
        sign: 0,
        style: 'collection',
        scrollTop: 0,
    },
    onLoad: function() {
        this.setData({ globalData: app.globalData })
    },
    onShow: function() {
        var that = this;
        if (app.globalData.userInfo) {
            that.setData({ userInfo: app.globalData.userInfo, isWxapp: that.isWxapp() });
        }
        that.getInfo();
    },
    onPullDownRefresh() {
        this.getInfo()
    },
    onSwipeout(e) {
        console.log('onSwipeout', e)
        const { id, type } = e.currentTarget.dataset

        app.request('/share/collectionInterface', { plan_id: id, cartype: type, identification: 1 }, (data, ret) => {
            console.log(data)
            this.getInfo()
            app.success(ret.msg)
        }, (data, ret) => {
            console.log(data)
            app.error(ret.msg)
        })

        this.setData({ toggle: !this.data.toggle })
    },
    login: function() {
        var that = this;
        app.login(function() {
            that.setData({ userInfo: app.globalData.userInfo, isWxapp: that.isWxapp() });
        });
    },
    isWxapp: function() {
        return app.globalData.userInfo ? app.globalData.userInfo.username.match(/^u\d+$/) : true;
    },
    showTips: function(event) {
        var tips = {
            balance: '余额通过插件的出售获得',
            score: '积分可以通过回答问题获得',
            level: '等级通过官网活跃进行升级',
        };
        var type = event.currentTarget.dataset.type;
        var content = tips[type];
        wx.showModal({
            title: '温馨提示',
            content: content,
            showCancel: false
        });
    },
    //点击头像上传
    uploadAvatar: function() {
        var that = this;
        wx.chooseImage({
            success: function(res) {
                var tempFilePaths = res.tempFilePaths;
                wx.uploadFile({
                    url: app.globalData.config.upload.uploadurl,
                    filePath: tempFilePaths[0],
                    name: 'file',
                    formData: app.globalData.config.upload.multipart,
                    success: function(res) {
                        var data = JSON.parse(res.data);
                        if (data.code == 200) {
                            app.request("/user/avatar", { avatar: data.url }, function(data, ret) {
                                app.success('头像上传成功!');
                                app.globalData.userInfo = data.userInfo;
                                that.setData({ userInfo: data.userInfo, isWxapp: that.isWxapp() });
                            }, function(data, ret) {
                                app.error(ret.msg);
                            });
                        }
                    },
                    error: function(res) {
                        app.error("上传头像失败!");
                    }
                });
            }
        });
    },
    onPageScroll(e) {
        this.setData({
            scrollTop : e.scrollTop,
        })
    },
    checkValue(items = []) {
        if (!items || !items.length) return false
        return items.map((n) => n.planList && n.planList.length > 0).includes(true)
    },
    onChange(e) {
        this.setData({
            style: e.detail.key,
        })
    },
    toMore() {
        wx.switchTab({
            url: '/page/index/index',
        })
    },
    toScore() {
        wx.navigateTo({
            url: '/page/my/score/index',
        })
    },
    integral() {
        app.integral('sign', function(data) {
            app.success(data)
        })
    },
    onOpenDetail(e) {
        const { id, type } = e.currentTarget.dataset

        wx.navigateTo({
            url: `/page/preference/detail/index?id=${id}&type=${type}`,
        })
    },
    getInfo() {
        app.request('/my/index', {}, (data, ret) => {
            console.log(data)
            this.setData({
                'collection.carSelectList': data.collection && data.collection.carSelectList,
                'collection.hasList': this.checkValue(data.collection && data.collection.carSelectList),
                'subscribe.carSelectList': data.subscribe && data.subscribe.carSelectList,
                'subscribe.hasList': this.checkValue(data.subscribe && data.subscribe.carSelectList),
                couponCount: data.couponCount,
                score: data.score,
                sign: data.sign,
                toggle: !this.data.toggle,
            })
            wx.stopPullDownRefresh()
        }, (data, ret) => {
            console.log(data)
            app.error(ret.msg)
        })
    },
    signIn() {
        if (this.data.sign === 1) {
            return app.info('已签到')
        }

        const user_id = app.globalData.userInfo.id

        app.request('/my/signIn', { user_id }, (data, ret) => {
            console.log(data)
            this.getInfo()
            app.success(ret.msg)
        }, (data, ret) => {
            console.log(data)
            app.error(ret.msg)
        })
    },
})
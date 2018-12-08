var Towxml = require('/assets/libs/towxml/main.js');
App({
    //请不要修改 /addons/cms/wxapp.这部分,只允许修改域名部分
    //请注意小程序只支持https
    apiUrl: 'https://crm.aicheyide.com/addons/cms/wxapp.',
    si: 0,
    //小程序启动
    onLaunch: function() {},
    // set globalData
    setGlobalData(data) {
        return wx.setStorageSync('globalData', data)
    },
    // get globalData
    getGlobalData() {
        return wx.getStorageSync('globalData')
    },
    // update globalData
    updateGlobalData(data) {
        var globalData = this.getGlobalData() || {}
        return wx.getStorageSync('globalData', Object.assign({}, globalData, data))
    },
    //投票
    vote: function(event, cb) {
        var that = this;
        var id = event.currentTarget.dataset.id;
        var type = event.currentTarget.dataset.type;
        var vote = wx.getStorageSync("vote") || [];
        if (vote.indexOf(id) > -1) {
            that.info("你已经发表过意见了,请勿重复操作");
            return;
        }
        vote.push(id);
        wx.setStorageSync("vote", vote);
        this.request('/archives/vote', { id: id, type: type }, function(data, ret) {
            typeof cb == "function" && cb(data);
        }, function(data, ret) {
            that.error(ret.msg);
        });
    },
    //添加积分
    integral: function(style, cb) {
        var that = this;
        this.request('/share/integral', { style }, function(data, ret) {
            typeof cb == "function" && cb(data);
            // that.success(ret.msg)
        }, function(data, ret) {
            that.error(ret.msg);
        });
    },
    //判断是否登录
    check: function(cb) {
        var that = this;
        if (that.getGlobalData().userInfo) {
            typeof cb == "function" && cb(that.getGlobalData().userInfo);
        } else {
            wx.getSetting({
                success: function(res) {
                    if (res.authSetting['scope.userInfo']) {
                        // 已经授权，可以直接调用 getUserInfo 获取头像昵称
                        wx.getUserInfo({
                            withCredentials: true,
                            success: function(res) {
                                that.login(cb);
                            },
                            fail: function() {
                                that.showLoginModal(cb);
                            }
                        });
                    } else {
                        that.showLoginModal(cb);
                    }
                },
                fail: function() {
                    that.showLoginModal(cb);
                }
            });
        }
    },
    //登录
    login: function(cb) {
        var that = this;
        var token = wx.getStorageSync('token') || '';
        //调用登录接口
        wx.login({
            success: function(res) {
                if (res.code) {
                    //发起网络请求
                    wx.getUserInfo({
                        success: function(ures) {
                            wx.request({
                                url: that.apiUrl + 'user/login?noAuth=1',
                                data: {
                                    code: res.code,
                                    rawData: ures.rawData,
                                    token: token
                                },
                                method: 'post',
                                header: {
                                    "Content-Type": "application/x-www-form-urlencoded",
                                },
                                success: function(lres) {
                                    var response = lres.data
                                    if (response.code == 1) {
                                        that.updateGlobalData(response.data)
                                        wx.setStorageSync('token', response.data.userInfo.token);
                                        typeof cb == "function" && cb(response.data.userInfo);
                                    } else {
                                        wx.setStorageSync('token', '');
                                        wx.setStorageSync('globalData', '');
                                        console.log("用户登录失败")
                                        that.showLoginModal(cb);
                                    }
                                }
                            });
                        },
                        fail: function(res) {
                            that.showLoginModal(cb);
                        }
                    });
                } else {
                    that.showLoginModal(cb);
                }
            }
        });
    },
    /**
     * 显示登录或授权提示
     * @param    {Function} cb [接口调用成功的回调函数]
     * @param    {Function} fail [接口调用失败的回调函数]
     * @param    {Boolean} isForce [是否强制显示登录或授权提示]
     * @return   {[type]} [description]
     */
    showLoginModal: function(cb, fail, isForce) {
        var that = this;
        if (!that.getGlobalData().userInfo || isForce) {
            //获取用户信息
            wx.getSetting({
                success: function(sres) {
                    if (sres.authSetting['scope.userInfo']) {
                        wx.showModal({
                            title: '温馨提示',
                            content: '当前无法获取到你的个人信息，部分操作可能受到限制',
                            confirmText: "重新登录",
                            cancelText: "暂不登录",
                            success: function(res) {
                                if (res.confirm) {
                                    that.login(cb);
                                } else {
                                    console.log('用户暂不登录');
                                    if (typeof fail === 'function') {
                                        fail.call(this, res)
                                    }
                                }
                            }
                        });
                    } else {
                        wx.showModal({
                            title: '温馨提示',
                            content: '当前无法获取到你的个人信息，部分操作可能受到限制',
                            confirmText: "去授权",
                            cancelText: "暂不授权",
                            success: function(res) {
                                if (res.confirm) {
                                    wx.navigateTo({
                                        url: '/page/my/setting/index?type=getuserinfo',
                                    });
                                    return false;
                                    wx.openSetting({
                                        success: function(sres) {
                                            that.check(cb);
                                        }
                                    });
                                } else {
                                    console.log('用户暂不授权');
                                    if (typeof fail === 'function') {
                                        fail.call(this, res)
                                    }
                                }
                            }
                        });
                    }
                }
            });
        } else {
            typeof cb == "function" && cb(that.getGlobalData().userInfo);
        }
    },
    //发起网络请求
    request: function(url, data, success, error) {
        var that = this;
        var userInfo = that.getGlobalData().userInfo || {};
        if (typeof data == 'function') {
            success = data;
            error = success;
            data = {};
        }

        // check userInfo
        if (userInfo) {
            data['user_id'] = userInfo.id;
            data['token'] = userInfo.token;

            // 判断是否需要授权，显示登录或授权提示
            if (!userInfo.id && url.indexOf('noAuth') === -1) {
                that.showLoginModal(function success() {
                    // 判断是否需要刷新当前页面
                    var ctx = getCurrentPages()[getCurrentPages().length - 1]
                    if (ctx && typeof ctx.onRefresh === 'function') {
                        ctx.onRefresh()
                    }
                }, function fail() {
                    // 返回上一页面
                    wx.navigateBack()
                });
                return;
            }
        }
        //移除最前的/
        while (url.charAt(0) === '/')
            url = url.slice(1);
        this.loading(true);
        wx.request({
            url: this.apiUrl + url,
            data: data,
            method: 'post',
            header: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            success: function(res) {
                that.loading(false);
                var code, msg, json;
                if (res.statusCode === 200) {
                    json = res.data;
                    if (json.code === 1) {
                        typeof success === 'function' && success(json.data, json);
                    } else {
                        typeof error === 'function' && error(json.data, json);
                    }
                } else {
                    json = typeof res.data === 'object' ? res.data : { code: 0, msg: '发生一个未知错误', data: null };
                    typeof error === 'function' && error(json.data, json);
                }
            },
            fail: function(res) {
                that.loading(false);
                console.log("fail:", res);
                typeof error === 'function' && error(null, { code: 0, msg: '', data: null });
            }
        });
    },
    //构造CDN地址
    cdnurl: function(url) {
        if (!url) return ''
        return url.toString().match(/^https?:\/\/(.*)/i) ? url : this.globalData.config.upload.cdnurl + url;
    },
    //文本提示
    info: function(msg, cb) {
        wx.showToast({
            title: msg,
            icon: 'none',
            duration: 2000,
            complete: function() {
                typeof cb == "function" && cb();
            }
        });
    },
    //成功提示
    success: function(msg, cb) {
        wx.showToast({
            title: msg,
            icon: 'success',
            image: '/assets/images/ok.png',
            duration: 2000,
            complete: function() {
                typeof cb == "function" && cb();
            }
        });
    },
    //错误提示
    error: function(msg, cb) {
        wx.showToast({
            title: msg,
            image: '/assets/images/error.png',
            duration: 2000,
            complete: function() {
                typeof cb == "function" && cb();
            }
        });
    },
    //警告提示
    warning: function(msg, cb) {
        wx.showToast({
            title: msg,
            image: '/assets/images/warning.png',
            duration: 2000,
            complete: function() {
                typeof cb == "function" && cb();
            }
        });
    },
    //Loading
    loading: function(msg) {
        if (typeof msg == 'boolean') {
            if (!msg) {
                if (!this.si) {
                    return;
                }
                clearTimeout(this.si);
                wx.hideLoading({});
                return;
            }
        }
        msg = typeof msg == 'undefined' || typeof msg == 'boolean' ? '加载中' : msg;
        this.globalData.loading = true;
        if (this.si) {
            return;
        }
        this.si = setTimeout(function() {
            wx.showLoading({
                title: msg
            });
        }, 300);

    },
    towxml: new Towxml(),
    //全局信息
    globalData: {
        // userInfo: null,
        config: null,
        indexTabList: [],
        newsTabList: [],
        productTabList: [],
        city: {
            cities_name: '成都',
            id: 38,
        },
    }
})
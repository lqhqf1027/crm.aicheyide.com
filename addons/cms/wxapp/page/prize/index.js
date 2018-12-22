import Wheel from '../components/wheel/wheel'
import getRand from '../../utils/getRand'

const app = getApp()

Page({
    data: {
        is_prize: 0,
    },
	onLoad() {
		this.getList()
	},
    /**
     * 初始化转盘
     */
    initWheel(areaNumber = 6, awardNumer = 1, is_prize = true, hasMobile = false) {
        this.wheel = new Wheel(this, {
            hasMobile,
            disabled: !is_prize,
            prizeUrl: '/page/components/wheel/images/wheel.png',
            btnUrl: `/page/components/wheel/images/${is_prize ? 'btn_yellow' : 'btn_grey'}.png`,
            areaNumber,
            speed: 16,
            awardNumer,
            mode: 2,
            callback: () => {
                const { prize, prizeList } = this.data
                const areaNumber = prizeList.length

                this.initWheel(areaNumber, prize && prize.flag, false, true)

                wx.showModal({
                    title: '提示',
                    content: `"抽奖成功"，礼品为：${prize.prize_name}，已放入"我的->我的奖品"`,
                    showCancel: false,
                    success: res => {
                        this.wheel.reset()
                        if (res.confirm) {
                            console.log('用户点击确定')
                        } else if (res.cancel) {
                            console.log('用户点击取消')
                        }
                    }
                })
            },
            getPhoneNumber: (e) => {
                console.log('getPhoneNumber', e)
                if (e.detail.errMsg === 'getPhoneNumber:ok') {
                    const params = {
                        iv: e.detail.iv,
                        encryptedData: e.detail.encryptedData,
                        sessionKey: app.getGlobalData().session_key
                    }

                    // 登录态检查
                    wx.checkSession({
                        success: () => {                    
                            this.prizeResult(params)
                        },
                        fail: () => {
                            app.showLoginModal(function(){}, function(){}, true)
                        },
                    })
                }
            },
            onStart: (e) => {
                this.prizeResult()
            },
        })
    },
    /**
     * 获取奖品列表
     */
	getList() {
        const city = wx.getStorageSync('city')

        app.request('/index/prizeShow?noAuth=1', { city_id: city.id }, (data, ret) => {
            console.log(data)
            this.setData({
                is_prize: data.is_prize,
                prizeList: data.prizeList,
            }, () => {
                this.initWheel(data.prizeList.length, 1, data.is_prize === 0, !!data.mobile)
            })
        }, (data, ret) => {
            console.log(data)
            app.error(ret.msg)
        })
    },
    /**
     * 领奖
     */
    prizeResult(params = {}) {
        console.log('prizeResult', params)

        const { prizeList, is_prize } = this.data
        const areaNumber = prizeList.length
        const prize = new getRand(prizeList, 'win_prize_number')

        // this.initWheel(areaNumber, prize.flag, true, true)
        // this.wheel.start(true)

        // return

        if (!prize || is_prize !== 0) return

        app.request('/index/prizeResult', { ...params, prize_id: prize.id }, (data, ret) => {
            console.log(data)
            this.setData({ is_prize: 1, prize }, () => {
                this.initWheel(areaNumber, prize.flag, true, true)
                this.wheel.start(true)
            })
            // app.success(ret.msg)
        }, (data, ret) => {
            console.log(data)
            app.error(ret.msg)
        })
    },
    onImageLoad(e) {
        console.log(e)
        const { type } = e.currentTarget.dataset
        const { width, height } = e.detail
        const imageStyle = `width: 100%; height: ${height}rpx`

        this.setData({
            imageStyle,
        })
    },
})
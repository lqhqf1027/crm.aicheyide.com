var app = getApp()
var items = [{
        type: 'radio',
        label: '默认排序',
        value: '1',
        children: [{
                label: '默认排序',
                value: '11',
            },
            {
                label: '销量最高',
                value: '12',
            },
        ],
    },
    {
        type: 'text',
        label: '品牌',
        value: '2',
    },
    {
        type: 'text',
        label: '首付',
        value: '3',
        children: [{
                label: '不限',
                value: '0-6',
            },
            {
                label: '1万以内',
                value: '0-1',
            },
            {
                label: '1-2万',
                value: '1-2',
            },
            {
                label: '2-3万',
                value: '2-3',
            },
            {
                label: '3-4万',
                value: '3-4',
            },
            {
                label: '4-5万',
                value: '4-5',
            },
            {
                label: '5万以上',
                value: '5-6',
            },
        ],
    },
    {
        type: 'text',
        label: '月供',
        value: '4',
        children: [{
                label: '不限',
                value: '0-6000',
            },
            {
                label: '2000元以内',
                value: '0-2000',
            },
            {
                label: '2000-3000元',
                value: '2000-3000',
            },
            {
                label: '3000-4000元',
                value: '3000-4000',
            },
            {
                label: '4000-5000元',
                value: '4000-5000',
            },
            {
                label: '5000元以上',
                value: '5000-6000',
            },
        ],
    },
]
var getPaymentLabel = (str) => {
    var value = str.map((n) => n / 10).join('-')
    var reslut = items[2].children.filter((n) => n.value === value)[0]
    if (reslut) {
        return reslut.label
    }
    return ''
}

Page({
    data: {
        items,
        globalData: app.globalData,
        shares: {},
        city: app.globalData.city,
        list: [],
        logisticsList: [],
        newcarList: [],
        usedcarList: [],
        allList: [],
        searchVal: {
            name: '',
            payment: [0, 0],
            monthly: [0, 0],
        },
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
            this.setData({
                logisticsList: data.logisticsList,
                newcarList: data.newcarList,
                usedcarList: data.usedcarList,
                allList: [...data.logisticsList, ...data.newcarList, ...data.usedcarList],
            }, () => {
                wx.getStorage({
                    key: 'searchVal',
                    success: ({ data }) => {
                        console.log(data)
                        wx.removeStorageSync('searchVal')
                        this.setCars(data)
                    },
                    fail: () => {
                        this.setCars()
                    },
                })
            })
        }, (data, ret) => {
            console.log(data)
            app.error(ret.msg)
        })
    },
    onChange(e) {
        console.log(e)
        this.setCars({...this.data.searchVal, style: e.detail.key })
    },
    setCars(searchVal = {}) {
        const { style, name, payment } = searchVal

        console.log(style, name)

        let list = []

        if (style === 'new') {
            list = [...this.data.newcarList]
        } else if (style === 'used') {
            list = [...this.data.usedcarList]
        } else if (style === 'logistics') {
            list = [...this.data.logisticsList]
        } else {
            list = [...this.data.allList]
        }

        if (name) {
            list = list.filter((n) => n.models_name && n.models_name.indexOf(name) !== -1)
        }

        // if (payment) {
        //     if (payment === '0-60') {
        //         continue
        //     } else if (payment === '50-60') {
        //         continue
        //     } else {
        //         const pay = payment.split('-').map((n) => Number(n) * 1000)

        //         list = list.filter((n) => n)
        //     }
        // }

        this.setData({
            searchVal,
            list,
        })
    },
    onTag(e) {
        const { name } = e.currentTarget.dataset
        const { searchVal } = this.data

        if (searchVal.name === name) {
            delete searchVal.name
            this.setCars(searchVal)
        }
    },
    onReset() {
        this.setCars()
    },
    onCancel() {
        const { index } = this.data
        const items = this.data.items.map((n, i) => {
            return Object.assign({}, n, {
                checked: index !== i ? n.checked : false,
                visible: index !== i ? n.checked : false,
            })
        })

        this.setData({
            items,
            backdrop: false,
        })
    },
    onClick(e) {
        const { index, checked } = e.currentTarget.dataset
        const items = this.data.items.map((n, i) => {
            return Object.assign({}, n, {
                checked: index === i ? !checked : false,
                visible: index === i ? !checked : false,
            })
        })

        this.setData({
            index,
            items,
            backdrop: index !== 1 && !checked,
        })
    },
    radioChange(e) {
        const { value } = e.detail
        const { index, item } = e.currentTarget.dataset
        const children = item.children.map((n) => Object.assign({}, n, {
            checked: n.value === value,
        }))
        const params = {
            [`items[${index}].children`]: children,
        }

        this.setData(params, this.onSelectChange)
    },
    onRadioChange(e) {
        const { value } = e.detail
        const { index, item } = e.currentTarget.dataset
        const children = item.children.map((n) => Object.assign({}, n, {
            checked: n.value === value,
        }))
        const params = {
            [`items[${index}].children`]: children,
        }

        if (index === 2) {
            params['searchVal.payment'] = value.split('-').map((n) => n * 10)
        } else if (index === 3) {
            params['searchVal.monthly'] = value.split('-').map((n) => n / 100)
        }

        console.log(params)

        this.setData(params, this.onSelectChange)
    },
    onPaymentChange(e) {
        const index = 2
        const item = this.data.items[index]
        const children = item.children.map((n) => Object.assign({}, n, {
            checked: false,
        }))
        console.log(e)
        this.setData({
            [`items[${index}].children`]: children,
            'searchVal.payment': e.detail.value,
        })
    },
    onMonthlyChange(e) {
        const index = 3
        const item = this.data.items[index]
        const children = item.children.map((n) => Object.assign({}, n, {
            checked: false,
        }))
        console.log(e)
        this.setData({
            [`items[${index}].children`]: children,
            'searchVal.monthly': e.detail.value,
        })
    },
    onSelectChange() {
        const items = this.data.items.map((n) => ({...n, checked: false, visible: false }))

        setTimeout(() => {
            this.setData({
                items,
                backdrop: false,
            })
        }, 300)
    },
})
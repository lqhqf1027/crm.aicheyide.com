const app = getApp()
const defaultItems = [{
        type: 'radio',
        label: '默认排序',
        value: '1',
        children: [{
                label: '推荐排序',
                value: '11',
            },
            {
                label: '首付最低',
                value: '12',
            },
            {
                label: '月供最低',
                value: '13',
            },
            {
                label: '人气最高',
                value: '14',
            },
            {
                label: '车价最低',
                value: '15',
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
                range: [0],
            },
            {
                label: '1万以内',
                value: '0-1',
                range: [0, 10000],
            },
            {
                label: '1-2万',
                value: '1-2',
                range: [10000, 20000],
            },
            {
                label: '2-3万',
                value: '2-3',
                range: [20000, 30000],
            },
            {
                label: '3-4万',
                value: '3-4',
                range: [30000, 40000],
            },
            {
                label: '4-5万',
                value: '4-5',
                range: [40000, 50000],
            },
            {
                label: '5万以上',
                value: '5-6',
                range: [50000],
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
                range: [0],
            },
            {
                label: '2000元以内',
                value: '0-2000',
                range: [0, 2000],
            },
            {
                label: '2000-3000元',
                value: '2000-3000',
                range: [2000, 3000],
            },
            {
                label: '3000-4000元',
                value: '3000-4000',
                range: [3000, 4000],
            },
            {
                label: '4000-5000元',
                value: '4000-5000',
                range: [4000, 5000],
            },
            {
                label: '5000元以上',
                value: '5000-6000',
                range: [5000],
            },
        ],
    },
]

const getRange = (value, array = [], num = 1) => {
    const reslut = array.filter(function(n) { return n.value === value })[0]
    if (reslut) {
        return reslut.range
    }
    return value && value !== '0-0' ? value.split('-').map((n) => Number(n) * num) : [0]
}

const checkValueInRange = (value = 0, min = 0, max = Infinity) => {
    return value >= min && value <= max
}

const defaultSearchValue = {
    sort: '',
    name: '',
    style: 'new',
    payment: [0, 0],
    monthly: [0, 0],
}

Page({
    data: {
        tabs: [{
            type: 'new',
            car_type_name: '新车',
        },{
            type: 'used',
            car_type_name: '二手车',
        },{
            type: 'logistics',
            car_type_name: '新能源车',
        }],
        items: defaultItems,
        globalData: app.globalData,
        shares: {},
        city: app.globalData.city,
        list: [],
        logisticsList: [],
        newcarList: [],
        usedcarList: [],
        allList: [],
        brandList: {},
        carBrandList: [],
        searchVal: {...defaultSearchValue },
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
    onBrand(e) {
        const { brand } = e.currentTarget.dataset

        this.setData({ 'searchVal.brand': brand }, this.onSelectChange)
    },
    onOpenDetail(e) {
        const { id, type } = e.currentTarget.dataset

        wx.navigateTo({
            url: `/page/preference/detail/index?id=${id}&type=${type}`,
        })
    },
    getBrandList() {
        let carBrandList = []
        const { brandList, searchVal } = this.data
        const data = brandList[searchVal.style]
        const words = Object.keys(data)

        words.forEach((item, index) => {
            carBrandList[index] = {
                key: item,
                list: data[item].map((n, i) => ({...n, key: n.id })),
            }
        })

        this.setData({ carBrandList })
    },
    getList() {
        const city = wx.getStorageSync('city')

        this.setData({
            city,
        })

        app.request('/carselection/index', { city_id: city.id }, (data, ret) => {
            console.log(data)

            // const tabs = data.carSelectList.map((n) => ({ car_type_name: n.car_type_name, type: n.type }))
            let logisticsList = []
            let newcarList = []
            let usedcarList = []
            let brandList = {}

            if (data.carSelectList.length > 0) {
                data.carSelectList.forEach((n) => {
                    console.log(n)
                    brandList[n.type] = {}
                    if (n.type === 'new') {
                        n.newCarList.forEach((m) => {
                            if (brandList[n.type][m.brand_initials] = brandList[n.type][m.brand_initials] || []) {
                                if (!brandList[n.type][m.brand_initials].map((n) => n.id).includes(m.id)) {
                                    brandList[n.type][m.brand_initials].push({
                                        id: m.id,
                                        name: m.name,
                                    })
                                }
                            }
                            newcarList = [...newcarList, ...m.planList.map((v) => ({...v, brand_id: m.id }))]
                        })
                    } else if (n.type === 'used') {
                        n.usedCarList.forEach((m) => {
                            if (brandList[n.type][m.brand_initials] = brandList[n.type][m.brand_initials] || []) {
                                if (!brandList[n.type][m.brand_initials].map((n) => n.id).includes(m.id)) {
                                    brandList[n.type][m.brand_initials].push({
                                        id: m.id,
                                        name: m.name,
                                    })
                                }
                            }
                            usedcarList = [...usedcarList, ...m.planList.map((v) => ({...v, brand_id: m.id }))]
                        })
                    } else if (n.type === 'logistics') {
                        n.logisticsCarList.forEach((m) => {
                            if (brandList[n.type][m.brand_initials] = brandList[n.type][m.brand_initials] || []) {
                                if (!brandList[n.type][m.brand_initials].map((n) => n.id).includes(m.id)) {
                                    brandList[n.type][m.brand_initials].push({
                                        id: m.id,
                                        name: m.name,
                                    })
                                }
                            }
                            logisticsList = [...logisticsList, ...m.planList.map((v) => ({...v, brand_id: m.id }))]
                        })
                    }
                })
            }

            this.setData({
                brandList,
                // tabs,
                logisticsList,
                newcarList,
                usedcarList,
                allList: [...logisticsList, ...newcarList, ...usedcarList],
            }, () => {
                wx.getStorage({
                    key: 'searchVal',
                    success: ({ data }) => {
                        console.log(data)
                        wx.removeStorageSync('searchVal')
                        this.setCars({...this.data.searchVal, ...data })
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
    setFilter(style) {
        const items = [...defaultItems]
        const searchVal = {...defaultSearchValue, style }

        if (style === 'used') {
            items[0].children.splice(2, 1)
            items.pop()
        }

        this.setData({
            items,
            searchVal,
            backdrop: false,
        }, this.setCars)
    },
    onChange(e) {
        console.log(e)
        this.setFilter(e.detail.key)
    },
    setCars(searchVal = this.data.searchVal) {
        const { sort, brand, style, name, payment, monthly } = searchVal

        console.log('searchVal', searchVal)

        let list = []

        // 按类型过滤
        if (style === 'new') {
            list = [...this.data.newcarList]
        } else if (style === 'used') {
            list = [...this.data.usedcarList]
        } else if (style === 'logistics') {
            list = [...this.data.logisticsList]
        } else {
            list = [...this.data.allList]
        }

        // 按名称过滤
        if (name) {
            list = list.filter((n) => n.models_name && n.models_name.indexOf(name) !== -1)
        }

        // 按品牌过滤
        if (brand) {
            list = list.filter((n) => n.brand_id === brand.id)
        }

        // 按首付过滤
        if (payment) {
            const value = payment.map((n) => Number(n) / 10).join('-')
            const range = getRange(value, defaultItems[2]['children'], 10000)
            console.log('payment', range)
            list = list.filter((n) => checkValueInRange(n.payment, range[0], range[1]))
        }

        // 按月供过滤
        if (monthly) {
            const value = monthly.map((n) => Number(n) * 100).join('-')
            const range = getRange(value, defaultItems[3]['children'])
            console.log('monthly', range)
            list = list.filter((n) => checkValueInRange(n.monthly, range[0], range[1]))
        }

        // 排序
        if (sort === '12') {
            list = list.sort((a, b) => a.payment - b.payment)
        } else if (sort === '13') {
            list = list.sort((a, b) => a.monthly - b.monthly)
        } else if (sort === '14') {
            list = list.sort((a, b) => b.popularity - a.popularity)
        } else if (sort === '15') {
            list = list.sort((a, b) => a.models.price - b.models.price)
        }

        console.log('list', list)

        this.setData({
            searchVal,
            list,
        })
    },
    onTag(e) {
        const { meta } = e.currentTarget.dataset
        const { searchVal, items } = this.data

        console.log('onTag', meta)

        if (meta === 'name') {
            searchVal.name = ''
        } else if (meta === 'brand') {
            searchVal.brand = ''
        } else if (meta === 'payment' || meta === 'monthly') {
            searchVal[meta] = [0, 0]
            const index = meta === 'payment' ? 2 : 3
            const children = items[index].children.map((n) => Object.assign({}, n, {
                checked: false,
            }))

            this.setData({
                [`items[${index}].children`]: children,
            })
        }

        console.log('searchVal', searchVal)

        this.setCars(searchVal)
    },
    onReset() {
        console.log('onReset', defaultSearchValue)
        this.setCars({...defaultSearchValue })
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

        if (index === 1) {
            this.getBrandList()
        }

        this.setData({
            index,
            items,
            backdrop: !checked,
        })
    },
    radioChange(e) {
        const { value } = e.detail
        const { index, item } = e.currentTarget.dataset
        const children = item.children.map((n) => Object.assign({}, n, {
            checked: n.value === value,
        }))
        const params = {
            'searchVal.sort': value,
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
        }, this.setCars)
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
        }, this.setCars)
    },
    onSelectChange() {
        const items = this.data.items.map((n) => ({...n, checked: false, visible: false }))

        setTimeout(() => {
            this.setData({
                items,
                backdrop: false,
            }, this.setCars)
        }, 300)
    },
})
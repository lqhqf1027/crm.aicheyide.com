const app = getApp()

Page({
    data: {
        brandList: [],
    },
    onLoad() {
        wx.getStorage({
            key: 'brandList',
            success: ({ data }) => {
                console.log(data)
                wx.removeStorageSync('brandList')

                let brandList = []
                const words = Object.keys(data)

                words.forEach((item, index) => {
                    brandList[index] = {
                        key: item,
                        list: data[item].map((n, i) => ({...n, key: n.id })),
                    }
                })

                this.setData({ brandList })
            },
        })
    },
    onClick(e) {
        const { value } = e.currentTarget.dataset
        console.log(value)
        wx.setStorage({
            key: 'searchVal',
            data: {
                brand: value,
            },
            success() {
                wx.switchTab({
                    url: '/page/index/index',
                })
            },
        })
    },
})
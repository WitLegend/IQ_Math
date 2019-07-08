//app.js
var qcloud = require('./vendor/wafer2-client-sdk/index')
var config = require('./config')

App({
  appData:{
    wssData:[],
    test:1,
    appId:config.service.appId,
    userInfo:{},
    openId:'',
    tunnelStatus: 'close',//统一管理唯一的信道连接的状态：connect、close、reconnecting、reconnect、error
    friendsFightingRoom: undefined,//好友对战时创建的唯一房间名,作为好友匹配的标识
  },
    onLaunch: function () {
      var that = this
        // qcloud.setLoginUrl(config.service.loginUrl)


      if (that.appData.openId == '') {
        wx.login({
          success(res) {
            if (res.code) {
              // 获得openid
              wx.request({
                url: 'https://api.weixin.qq.com/sns/jscode2session?appid=' + config.service.appId + '&secret=' + config.service.secret + '&js_code=' + res.code + '&grant_type=authorization_code',
                data: {},
                success(res) {
                  that.appData.openId = res.data.openid
                  //获得用户的信息，从数据库
                  wx.request({
                    url: config.service.getuserinfoUrl,
                    data: {
                      openid: that.appData.openId
                    },
                    success(res) {
                      if (JSON.stringify(res.data.data) !== '[]') {
                        that.appData.userInfo = res.data.data
                      } else {
                        //数据库中没有用户的信息，让用户授权
                        wx.reLaunch({
                          url: "/pages/login/login",
                        })
                      }
                    }
                  })
                },
              })
            } else {
              console.log('登录失败！' + res.errMsg)
            }
          }
        })
      }

      
    },


  pageGetUserInfo(page, openIdReadyCallback) { //在page中获取用户信息
    const userInfo = wx.getStorageSync('user_info_F2C224D4-2BCE-4C64-AF9F-A6D872000D1A')
    if (userInfo) {
      page.setData({
        userInfo,
        openId: userInfo.openId
      })
      this.appData.openId = userInfo.openId
      if (openIdReadyCallback) {
        openIdReadyCallback(userInfo.openId)
      }
    } else {
      this.userInfoReadyCallback = (userInfo) => {  //获取用户信息后的回调函数
        page.setData({  //每个page都会自动存储userInfo和openId
          userInfo,
          openId: userInfo.openId
        })
        if (openIdReadyCallback) {  //如果设置了openid的回调函数，则调用回调
          openIdReadyCallback(userInfo.openId)
        }
      }
    }
  },

})
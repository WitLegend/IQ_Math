// pages/index/index.js
var qcloud = require('../../vendor/wafer2-client-sdk/index')
var util = require('../../utils/util.js')
var config = require('../../config')
const app = getApp();


Page({

  /**
   * 页面的初始数据
   */
  data: {
    canIUse:wx.canIUse('button.open-type.getUserInfo')
  },


  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    wx.login({
      success(res) {
        if (res.code) {
          // 发起网络请求
          wx.request({
            url: 'https://api.weixin.qq.com/sns/jscode2session?appid=' + config.service.appId + '&secret=' + config.service.secret +'&js_code='+ res.code+'&grant_type=authorization_code',
            data: {},
            success(res){
              app.appData.openId = res.data.openid
            },
          })
        } else {
          console.log('登录失败！' + res.errMsg)
        }
      }
    })

    // wx.getSetting({
    //   success:function(res){
    //     if (res.authSetting['scope.userInfo']) {
    //       console.log(res)
    //       console.log(app.appData.userInfo)
    //     }
    //   }
    // })
  },

  
  onGotUserInfo:function(e){
    console.log(e.detail.userInfo)
    // console.log()
    if(e.detail.userInfo)
    {
      app.appData.userInfo = e.detail.userInfo
      // console.log(e.detail.userInfo)
      wx.request({
        url: config.service.setuserinfoUrl,
        data:{
          userInfo: e.detail.userInfo,
          openid: app.appData.openId,
        },
        success(res){
          if(res.data.code == 0)
          {
            wx.navigateTo({
              url: '/pages/index/index',
            })
          }
        }
      })
    }
  },

  /**
   * 生命周期函数--监听页面初次渲染完成
   */
  onReady: function () {
    
  },

  /**
   * 生命周期函数--监听页面显示
   */
  onShow: function () {
    
  },

  /**
   * 生命周期函数--监听页面隐藏
   */
  onHide: function () {
    
  },

  /**
   * 生命周期函数--监听页面卸载
   */
  onUnload: function () {
    
  },

  /**
   * 页面相关事件处理函数--监听用户下拉动作
   */
  onPullDownRefresh: function () {
    
  },

  /**
   * 页面上拉触底事件的处理函数
   */
  onReachBottom: function () {
    
  },

  /**
   * 用户点击右上角分享
   */
  onShareAppMessage: function () {
    
  }
})
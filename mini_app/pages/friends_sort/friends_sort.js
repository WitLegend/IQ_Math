// pages/friends_sort/friends_sort.js
let qcloud = require('../../vendor/wafer2-client-sdk/index')
let config = require('../../config')
let util = require('../../utils/util.js')
const app = getApp();

Page({

  /**
   * 页面的初始数据
   */
  data: {
    // question_sort: [],     //问题类型
    // sortId: '',
    // sortName: '',
    showShareButton: true,
    is_share:false,
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    app.appData.fromClickId = options.currentClickId
    app.upDateUser_networkFromClickId = require('../../utils/upDateUser_networkFromClickId.js').upDateUser_networkFromClickId
    wx.showShareMenu({
      withShareTicket: true
    })
    app.pageGetUserInfo(this)
    // this.getFriends_sort()   //todo
  },
  //取消邀请，返回到首页
  closeShareButton() {
    wx.navigateBack({
      delta:1
    })
  },

//请求问题类型     可以删除
  getFriends_sort(){
    //util.showBusy('正在请求');
    qcloud.request({
      login: false,
      url: `${app.appData.baseUrl}question_sort`,
      success: (res) => {
        // util.showSuccess('请求成功完成');
        let data0 = res.data.data;
        this.setData({
          question_sort: data0
        })
      },
      fail(error) {
        util.showModel('请求失败', error);
        console.log('request fail', error);
      },
    });
  },

  onShareAppMessage(res){
    const that = this
    setTimeout(function () {
      that.setData({
        is_share: true
      })
    },2500)


    let val = {
      room_id: app.appData.openId,
      openid: app.appData.openId,
      type: 'new_room'
    }
    if (app.appData.wss && app.appData.wss.readyState == 1){
      app.appData.wss.send({
        data: JSON.stringify(val),
        success: function (res) {}
      })
    }else{
      app.appData.wss = wx.connectSocket({
        url: config.service.socket,
      })
      app.appData.wss.onOpen(function (res) {
        app.appData.wss.send({
          data: JSON.stringify(val),
          success: function (res) {}
        })
      })
      app.appData.wss.onMessage(function (res) {
        app.appData.wssData = JSON.parse(res.data).data
        console.log(JSON.parse(res.data).data)
      })
    }
    // setTimeout(function(res){
    //   console.log(app.appData.wssData)
    // },500)
    

    // app.appData.friendsFightingRoom = new Date().getTime().toString() + parseInt(Math.random() * 10000000)//创建:时间+随机数
    return {
      title: '谁才是算数领域的天才？比比看吧！',
      path: '/pages/friends_match/friends_match?status=2&room_id=' + app.appData.openId,
    }
  },

  into_room(){
    wx.navigateTo({
      url: '../friends_match/friends_match?status=1&room_id='+app.appData.openId,
    })
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
})
// pages/fighting_room/fighting_room.js
const app = getApp();
var config = require('../../config')

Page({

  /**
   * 页面的初始数据
   */
  data: {
    roomName: '',//对战房间号
    userInfo_me: '', //本人用户信息
    userInfo_others: '',//对手用户信息
    countdown: 10,//倒计时
    question: '',//websocket服务器传过来的问题及答案
    hasClick: false,//判断是否已选答案，不能重新选择
    localClick: false,//是否本地单击的答案
    tunnelIdReplacing: 0,//tunnelIdReplacing存在2种转态：0表示不存在信道替换，1表示信道正在替换中:禁止发送数据
    clickIndex: '',//判断用户选择了哪个答案
    answerColor: '',//根据选择正确与否给选项添加背景颜色
    scoreMyself: 0,//自己的总分
    status_users_others: {
      openId: '', //对手的openid
      userChoose: '',//对手选择了第几项
      answerColor: '',//对手是否选择正确
      status:'',//对手回答的顺序
    },//对手的答题状态
    score_others: 0,//对手的总分
    game_over: false,  //判断此次PK是否结束
    win: 2,  //0:表示输，1：表示赢,2:表示平手
    sendNumber: 0,//每一轮的答题次数不能超过1次
    question_num : 5,
    sure_num : 0,
    temp:{},
  },

  /**
   * 生命周期函数--监听页面加载
   */
  onLoad: function (options) {
    var that = this
    that.setData({
      room_id:options.room_id,
      status:options.status,
      userInfo_me: app.appData.userInfo,
      userInfo_others: app.appData.other_info,
    })
    that.data.status_users_others.openId = that.data.userInfo_others.openid
    wx.showShareMenu({
      withShareTicket: true
    })
    this.startAnimate()//定义开始动画

    let val = {
      room_id: options.room_id,
      openid: app.appData.openId,
      type: 'get_question'
    }
    if(options.status == 2){
      app.appData.wss.send({
        data: JSON.stringify(val),
        success: function (res) {
          setTimeout(function () {
            that.setData({
              question: app.appData.wssData.question              
            })
            app.appData.wssData.status = 'old'
          }, 500)
        }
      })
    }else{
      setTimeout(function () {
        that.setData({
          question: app.appData.wssData.question
        })
        app.appData.wssData.status = 'old'
      }, 500)
    }

    setTimeout(function(){
      that.setData({ animate_showChoice: 'fadeIn' })
    },2000)

    this.pk_begin()
    this.time_out()
  },

  time_out() {
    // let countdown = that.data.countdown;
    const that = this
    var timer
    timer = setInterval(function () {
      // countdown--
      that.setData({
        countdown: that.data.countdown - 1
      })
      if (that.data.countdown == 0 && !that.data.finsh && that.data.status == 1) {
        that.get_nextquestion()
        that.setData({
          countdown:10
        })
        clearInterval(timer)
      }
    }, 1000)
  },

  onShareAppMessage(res) {
    const that = this;
    return {
      title: '谁才是算数天才？比比看吧！',
      path: `/pages/index/index`,
    }
  },

  pk_begin(res){
    var that = this

    let over_timer
    over_timer = setInterval(function(){
      if (that.data.question_num <= 0) {
        clearInterval(over_timer)
        let val = {
          room_id : that.data.room_id,
          openid : app.appData.openId,
          type : 'game_over',
          true_num: that.data.sure_num,
          score: that.data.scoreMyself
        }
        app.appData.wss.send({
          data : JSON.stringify(val)
        })
        that.setData({
          game_over: true,
        })
        if(that.data.scoreMyself > that.data.score_others){
          that.setData({
            win:1
          })
        }
        if (that.data.scoreMyself < that.data.score_others) {
          that.setData({
            win: 0
          })
        }
        if(that.data.status == 1){
          setTimeout(function () {
            let val = {
              room_id: that.data.room_id,
              openid: app.appData.openId,
              type: 'insert',
            }
            app.appData.wss.send({
              data:JSON.stringify(val)
            })
          }, 2000)
        }
      }
      if (app.appData.wssData.type == 'get_question' && app.appData.wssData.status == 'new'){
        if((that.data.localClick && that.data.status_users_others.userChoose) || (that.data.countdown <= 0)){
          console.log(11)
          app.appData.wssData.status = 'old'
          setTimeout(function(){
            // that.data.question = app.appData.wssData.question
            that.data.animate_showChoice = false
            that.setData({
              question: app.appData.wssData.question,
              animate_showChoice: '',
              countdown: 10,
              localClick: false,
              hasClick: false,
              clickIndex: '',
              status_users_others: {
                openId: that.data.userInfo_others.openid,
                userChoose: '',
                answerColor: ''
              },
              answerColor: '',
              sendNumber: 0,
              animate_rightAnswer: '',
              temp: {},
              question_num : that.data.question_num - 1
            })
          },1000)
        }
        setTimeout(() => {//2S后显示选项和开始倒计时
          that.setData({
            animate_showChoice: 'fadeIn',
            // countdown:10
          })
          // that.time_out()
          // timerCountdown = setInterval(function () {
          //   countdown--
          //   that.setData({
          //     countdown
          //   })
          //   if (countdown == 0) {
          //     clearInterval(timerCountdown)
          //   }
          // }, 1000)
        }, 1000)
      }
    },100)
  },

  answer(e) {//开始答题
    const that = this
    if (!that.data.localClick) {  //防止重新选择答案
      if (e.currentTarget.dataset.right) {//判断答案是否正确
        that.setData({
          clickIndex: e.currentTarget.dataset.index,
          answerColor: 'right',
          sure_num:that.data.sure_num + 1,
        })
        //答对了则加分，时间越少加分越多,总分累加
        that.setData({
          scoreMyself: that.data.scoreMyself + that.data.countdown * 10
        })
      } else {
        that.setData({
          clickIndex: e.currentTarget.dataset.index,
          answerColor: 'error'
        })
      }
      that.setData({
        localClick: true//本地已经点击,若hasClick仍未false，则说明没有发送数据出去
      })
      let val = {
        room_id : that.data.room_id,
        openid : app.appData.openId,
        type : 'answer',
        userChoose : that.data.clickIndex,
        answerColor : that.data.answerColor,
        score_others:that.data.scoreMyself
      }
      app.appData.wss.send({
        data:JSON.stringify(val),
        // success:function(){
        //   that.setData({
        //     temp: app.appData.wssData
        //   })
        // }
      })
      that.data.temp = app.appData.wssData
      // 选择后判断对手是否选择，没有的话就监听对手的状态
      if (that.data.temp.type  == 'answer'){
        console.log("后回答",that.data.temp)
        that.setData({
          score_others: that.data.temp.score_others,
          status_users_others: {
            openId: that.data.userInfo_others.openid,
            userChoose: that.data.temp.userChoose,
            answerColor: that.data.temp.answerColor,
            status: that.data.temp.status,
          }
        })
        that.data.temp = []
        that.data.animate_showChoice = false
        setTimeout(function(){
          that.get_nextquestion()
        },1500)
      }else{
        let other_timer
        other_timer = setInterval(function () {
          that.data.temp = app.appData.wssData
          if(that.data.temp.type == 'answer'){
            that.setData({
              score_others: that.data.temp.score_others,
              status_users_others : {
                openId: that.data.userInfo_others.openid,
                userChoose: that.data.temp.userChoose,
                answerColor: that.data.temp.answerColor,
                status: that.data.temp.status,
              }
            })
            console.log('先回答',that.data.temp)
            if (that.data.status_users_others.status == '2') {
              console.log('判断谁去获取题目')
              that.get_nextquestion()
            }
            that.data.temp = []
            that.data.animate_showChoice = false
            clearInterval(other_timer)
          }
        }, 50)
      }
      // that.sendAnswer(that)
    }
  },

  get_nextquestion(){
    var that = this
    let val = {
      room_id: that.data.room_id,
      openid: app.appData.openId,
      type: 'get_question',
    }
    app.appData.wss.send({
      data:JSON.stringify(val)
    })
  },

  continue_fighting() {
    wx.reLaunch({
      url: '../index/index',
    })
  },

  startAnimate() {
    const that = this
    that.setData({
      zoomIn: 'zoomIn'
    })
    setTimeout(function () {
      that.setData({
        zoomOut: 'zoomOut'
      })
    }, 1500)
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
import axios from 'axios'

let base = '';
export const postRequest = (url, params, token) => {
  return axios({
    method: 'post',
    url: `${base}${url}`,
    data: params,
    transformRequest: [function (data) {
      // Do whatever you want to transform the data
      let ret = ''
      for (let it in data) {
        ret += encodeURIComponent(it) + '=' + encodeURIComponent(data[it]) + '&'
      }
      return ret
    }],
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'Authorization': token
    }
  });
}
export const uploadFileRequest = (url, params, token) => {
  return axios({
    method: 'post',
    url: `${base}${url}`,
    data: params,
    headers: {
      'Content-Type': 'multipart/form-data',
      'Authorization': token
    }
  });
}
export const putRequest = (url, params, token) => {
  return axios({
    method: 'put',
    url: `${base}${url}`,
    data: params,
    transformRequest: [function (data) {
      let ret = ''
      for (let it in data) {
        ret += encodeURIComponent(it) + '=' + encodeURIComponent(data[it]) + '&'
      }
      return ret
    }],
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'Authorization': token
    }
  });
}
export const deleteRequest = (url, params, token) => {
  return axios({
    method: 'delete',
    url: `${base}${url}`,
    data: params,
    transformRequest: [function (data) {
        let ret = ''
        for (let it in data) {
          ret += encodeURIComponent(it) + '=' + encodeURIComponent(data[it]) + '&'
        }
        return ret
      }],
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'Authorization': token
      }
  });
}
export const getRequest = (url,params, token) => {
  return axios({
    method: 'get',
    url: `${base}${url}`,
    data:params,
    params:params,
    transformRequest: [function (data) {
      let ret = '?'
      for (let it in data) {
        ret += encodeURIComponent(it) + '=' + encodeURIComponent(data[it]) + '&'
      }
      return ret
    }],
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'Authorization': token
    }
  });
}


class MyUploadAdapter {
    constructor(loader) {
        this.loader = loader; // 文件加载器
        this.url = '/core/upload/upload'; // 后端处理上传的 URL
    }

    upload() {
        return new Promise((resolve, reject) => {
            // 解析 loader.file 以获取实际的文件对象
            this.loader.file
                .then(file => {
                    const formData = new FormData();
                    formData.append('file', file); // 添加解析后的文件

                    // 执行上传请求
                    return fetch(this.url, {
                        method: 'POST',
                        body: formData,
                    });
                })
                .then(response => {
                    if (!response.ok) {
                        return reject(response);
                    }
                    return response.json(); // 解析响应的 JSON
                })
                .then(data => {
                    resolve({ default: data.url }); // 返回文件的 URL
                })
                .catch(reject); // 错误处理
        });
    }
}

// 创建上传适配器的功能
function createUploadAdapter(loader) {
    return new MyUploadAdapter(loader);
}
package com.decan.movie

import android.annotation.SuppressLint
import android.app.AlertDialog
import android.app.DownloadManager
import android.content.ActivityNotFoundException
import android.content.Context
import android.content.Intent
import android.graphics.Bitmap
import android.net.ConnectivityManager
import android.net.Network
import android.net.NetworkCapabilities
import android.net.Uri
import android.os.Build
import android.os.Bundle
import android.os.Message
import android.provider.Settings
import android.view.View
import android.view.Window
import android.view.WindowInsets
import android.view.WindowInsetsController
import android.webkit.CookieManager
import android.webkit.DownloadListener
import android.webkit.HttpAuthHandler
import android.webkit.JsResult
import android.webkit.RenderProcessGoneDetail
import android.webkit.ServiceWorkerClient
import android.webkit.ServiceWorkerController
import android.webkit.SslErrorHandler
import android.webkit.URLUtil
import android.webkit.ValueCallback
import android.webkit.WebChromeClient
import android.webkit.WebResourceError
import android.webkit.WebResourceRequest
import android.webkit.WebResourceResponse
import android.webkit.WebSettings
import android.webkit.WebView
import android.webkit.WebViewClient
import android.widget.FrameLayout
import android.widget.LinearLayout
import android.widget.TextView
import android.widget.Toast
import androidx.activity.OnBackPressedCallback
import androidx.appcompat.app.AppCompatActivity
import androidx.core.content.ContextCompat
import java.net.URISyntaxException

class MainActivity : AppCompatActivity() {

    private lateinit var root: FrameLayout
    private lateinit var webView: WebView
    private lateinit var loadingPanel: LinearLayout
    private lateinit var errorPanel: LinearLayout
    private lateinit var nativeBar: LinearLayout
    private lateinit var retryButton: TextView
    private var uploadCallback: ValueCallback<Array<Uri>>? = null
    private var lastLoadedUrl: String = START_URL
    private var pageLoadFailed = false
    private var networkCallback: ConnectivityManager.NetworkCallback? = null
    private var isDestroyed = false

    private val homeButton by lazy { findViewById<TextView>(R.id.homeButton) }
    private val accountButton by lazy { findViewById<TextView>(R.id.accountButton) }
    private val reloadButton by lazy { findViewById<TextView>(R.id.reloadButton) }
    private val moreButton by lazy { findViewById<TextView>(R.id.moreButton) }

    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        requestWindowFeature(Window.FEATURE_NO_TITLE)
        setContentView(R.layout.activity_main)
        initializeViews()
        configureWindow()
        configureWebView()
        configureNativeControls()
        configureBackNavigation()
        configureNetworkMonitoring()
        restoreWebViewState(savedInstanceState)
    }

    private fun initializeViews() {
        root = findViewById(R.id.root)
        webView = findViewById(R.id.webView)
        loadingPanel = findViewById(R.id.loadingPanel)
        errorPanel = findViewById(R.id.errorPanel)
        nativeBar = findViewById(R.id.nativeBar)
        retryButton = findViewById(R.id.retryButton)
    }

    private fun configureWindow() {
        window.statusBarColor = ContextCompat.getColor(this, R.color.decan_black)
        window.navigationBarColor = ContextCompat.getColor(this, R.color.decan_black)
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.R) {
            window.insetsController?.setSystemBarsAppearance(
                0,
                WindowInsetsController.APPEARANCE_LIGHT_STATUS_BARS or WindowInsetsController.APPEARANCE_LIGHT_NAVIGATION_BARS
            )
        } else {
            @Suppress("DEPRECATION")
            window.decorView.systemUiVisibility = 0
        }
    }

    @SuppressLint("SetJavaScriptEnabled")
    private fun configureWebView() {
        val settings = webView.settings
        settings.javaScriptEnabled = true
        settings.domStorageEnabled = true
        settings.databaseEnabled = true
        settings.loadsImagesAutomatically = true
        settings.mediaPlaybackRequiresUserGesture = false
        settings.javaScriptCanOpenWindowsAutomatically = true
        settings.setSupportMultipleWindows(true)
        settings.setSupportZoom(false)
        settings.builtInZoomControls = false
        settings.displayZoomControls = false
        settings.useWideViewPort = true
        settings.loadWithOverviewMode = false
        settings.allowFileAccess = false
        settings.allowContentAccess = true
        settings.mixedContentMode = WebSettings.MIXED_CONTENT_NEVER_ALLOW
        settings.cacheMode = WebSettings.LOAD_DEFAULT
        settings.userAgentString = buildUserAgent(settings.userAgentString)
        CookieManager.getInstance().setAcceptCookie(true)
        CookieManager.getInstance().setAcceptThirdPartyCookies(webView, true)
        WebView.setWebContentsDebuggingEnabled(BuildConfig.DEBUG)

        webView.overScrollMode = View.OVER_SCROLL_NEVER
        webView.isVerticalScrollBarEnabled = false
        webView.isHorizontalScrollBarEnabled = false
        webView.setBackgroundColor(ContextCompat.getColor(this, R.color.decan_black))
        webView.webViewClient = DecanWebViewClient()
        webView.webChromeClient = DecanChromeClient()
        webView.setDownloadListener(DecanDownloadListener())
        configureServiceWorker()
    }

    private fun buildUserAgent(defaultAgent: String): String {
        val appAgent = " DecanMovie/${BuildConfig.VERSION_NAME} Android/${Build.VERSION.RELEASE}"
        return if (defaultAgent.contains("DecanMovie/")) defaultAgent else defaultAgent + appAgent
    }

    private fun configureServiceWorker() {
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
            ServiceWorkerController.getInstance().setServiceWorkerClient(object : ServiceWorkerClient() {
                override fun shouldInterceptRequest(request: WebResourceRequest): WebResourceResponse? {
                    return null
                }
            })
        }
    }

    private fun configureNativeControls() {
        homeButton.setOnClickListener { navigateTo(START_URL) }
        accountButton.setOnClickListener { navigateTo("$START_URL/account.html") }
        reloadButton.setOnClickListener { reloadCurrentPage() }
        moreButton.setOnClickListener { showMoreMenu() }
        retryButton.setOnClickListener { retryFromError() }
    }

    private fun navigateTo(url: String) {
        if (!isAllowedWebUrl(url)) {
            openExternal(Uri.parse(url))
            return
        }
        if (!hasNetworkConnection()) {
            showError()
            return
        }
        hideError()
        showLoading()
        webView.loadUrl(url)
    }

    private fun reloadCurrentPage() {
        if (!hasNetworkConnection()) {
            showError()
            return
        }
        hideError()
        showLoading()
        if (webView.url.isNullOrBlank()) {
            webView.loadUrl(START_URL)
        } else {
            webView.reload()
        }
    }

    private fun retryFromError() {
        hideError()
        showLoading()
        val target = if (lastLoadedUrl.isBlank()) START_URL else lastLoadedUrl
        webView.loadUrl(target)
    }

    private fun configureBackNavigation() {
        onBackPressedDispatcher.addCallback(this, object : OnBackPressedCallback(true) {
            override fun handleOnBackPressed() {
                when {
                    webView.canGoBack() -> webView.goBack()
                    else -> finish()
                }
            }
        })
    }

    private fun configureNetworkMonitoring() {
        val manager = getSystemService(Context.CONNECTIVITY_SERVICE) as ConnectivityManager
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N) {
            networkCallback = object : ConnectivityManager.NetworkCallback() {
                override fun onAvailable(network: Network) {
                    runOnUiThread {
                        if (!isDestroyed && pageLoadFailed) retryFromError()
                    }
                }
            }
            manager.registerDefaultNetworkCallback(networkCallback!!)
        }
    }

    private fun hasNetworkConnection(): Boolean {
        val manager = getSystemService(Context.CONNECTIVITY_SERVICE) as ConnectivityManager
        val network = manager.activeNetwork ?: return false
        val capabilities = manager.getNetworkCapabilities(network) ?: return false
        return capabilities.hasCapability(NetworkCapabilities.NET_CAPABILITY_INTERNET)
    }

    private fun showLoading() {
        loadingPanel.visibility = View.VISIBLE
        errorPanel.visibility = View.GONE
        pageLoadFailed = false
    }

    private fun hideLoading() {
        loadingPanel.visibility = View.GONE
    }

    private fun showError() {
        loadingPanel.visibility = View.GONE
        errorPanel.visibility = View.VISIBLE
        pageLoadFailed = true
    }

    private fun hideError() {
        errorPanel.visibility = View.GONE
        pageLoadFailed = false
    }

    private fun restoreWebViewState(savedInstanceState: Bundle?) {
        if (savedInstanceState != null) {
            webView.restoreState(savedInstanceState)
            lastLoadedUrl = webView.url ?: START_URL
            return
        }
        if (hasNetworkConnection()) {
            webView.loadUrl(START_URL)
        } else {
            showError()
        }
    }

    override fun onSaveInstanceState(outState: Bundle) {
        webView.saveState(outState)
        super.onSaveInstanceState(outState)
    }

    override fun onPause() {
        super.onPause()
        webView.onPause()
        CookieManager.getInstance().flush()
    }

    override fun onResume() {
        super.onResume()
        webView.onResume()
    }

    override fun onDestroy() {
        isDestroyed = true
        val manager = getSystemService(Context.CONNECTIVITY_SERVICE) as ConnectivityManager
        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.N && networkCallback != null) {
            try {
                manager.unregisterNetworkCallback(networkCallback!!)
            } catch (_: Exception) {
            }
        }
        uploadCallback?.onReceiveValue(null)
        uploadCallback = null
        webView.apply {
            stopLoading()
            webChromeClient = null
            webViewClient = null
            removeAllViews()
            destroy()
        }
        super.onDestroy()
    }

    private fun showMoreMenu() {
        val items = arrayOf(
            "Open in browser",
            "Share Decan Movie",
            "Clear page cache",
            "App settings"
        )
        AlertDialog.Builder(this)
            .setTitle("Decan Movie")
            .setItems(items) { _, which ->
                when (which) {
                    0 -> openExternal(Uri.parse(webView.url ?: START_URL))
                    1 -> shareApp()
                    2 -> clearPageCache()
                    3 -> openAppSettings()
                }
            }
            .setNegativeButton("Close", null)
            .show()
    }

    private fun shareApp() {
        val intent = Intent(Intent.ACTION_SEND).apply {
            type = "text/plain"
            putExtra(Intent.EXTRA_TEXT, START_URL)
            putExtra(Intent.EXTRA_SUBJECT, "Decan Movie")
        }
        startActivity(Intent.createChooser(intent, "Share Decan Movie"))
    }

    private fun clearPageCache() {
        webView.clearCache(true)
        webView.clearHistory()
        Toast.makeText(this, "Page cache cleared", Toast.LENGTH_SHORT).show()
        navigateTo(START_URL)
    }

    private fun openAppSettings() {
        val intent = Intent(Settings.ACTION_APPLICATION_DETAILS_SETTINGS).apply {
            data = Uri.parse("package:$packageName")
        }
        openExternal(intent)
    }

    private fun openExternal(intent: Intent) {
        try {
            startActivity(intent)
        } catch (_: ActivityNotFoundException) {
            Toast.makeText(this, "No compatible app is available", Toast.LENGTH_SHORT).show()
        }
    }

    private fun openExternal(uri: Uri) {
        openExternal(Intent(Intent.ACTION_VIEW, uri))
    }

    private fun isAllowedWebUrl(url: String): Boolean {
        return try {
            val uri = Uri.parse(url)
            val host = uri.host?.lowercase() ?: return false
            uri.scheme == "https" && (host == ALLOWED_HOST || host.endsWith(".vercel.app"))
        } catch (_: Exception) {
            false
        }
    }

    private fun handleUri(uri: Uri): Boolean {
        val scheme = uri.scheme?.lowercase() ?: return false
        if (scheme == "http" || scheme == "https") {
            return if (isAllowedWebUrl(uri.toString())) {
                webView.loadUrl(uri.toString())
                true
            } else {
                openExternal(uri)
                true
            }
        }
        if (scheme == "intent") {
            return handleIntentUri(uri.toString())
        }
        openExternal(uri)
        return true
    }

    private fun handleIntentUri(value: String): Boolean {
        return try {
            val intent = Intent.parseUri(value, Intent.URI_INTENT_SCHEME)
            val fallback = intent.getStringExtra("browser_fallback_url")
            if (intent.resolveActivity(packageManager) != null) {
                startActivity(intent)
            } else if (!fallback.isNullOrBlank()) {
                handleUri(Uri.parse(fallback))
            }
            true
        } catch (_: URISyntaxException) {
            false
        } catch (_: ActivityNotFoundException) {
            false
        }
    }

    private inner class DecanWebViewClient : WebViewClient() {
        override fun shouldOverrideUrlLoading(view: WebView, request: WebResourceRequest): Boolean {
            return handleUri(request.url)
        }

        @Suppress("DEPRECATION")
        override fun shouldOverrideUrlLoading(view: WebView, url: String): Boolean {
            return handleUri(Uri.parse(url))
        }

        override fun onPageStarted(view: WebView, url: String, favicon: Bitmap?) {
            lastLoadedUrl = url
            pageLoadFailed = false
            showLoading()
            super.onPageStarted(view, url, favicon)
        }

        override fun onPageFinished(view: WebView, url: String) {
            lastLoadedUrl = url
            hideLoading()
            hideError()
            super.onPageFinished(view, url)
        }

        override fun onReceivedError(view: WebView, request: WebResourceRequest, error: WebResourceError) {
            if (request.isForMainFrame) showError()
            super.onReceivedError(view, request, error)
        }

        @Suppress("DEPRECATION")
        override fun onReceivedError(view: WebView, errorCode: Int, description: String?, failingUrl: String?) {
            if (failingUrl == view.url) showError()
            super.onReceivedError(view, errorCode, description, failingUrl)
        }

        override fun onReceivedHttpError(view: WebView, request: WebResourceRequest, errorResponse: WebResourceResponse) {
            if (request.isForMainFrame && errorResponse.statusCode >= 500) showError()
            super.onReceivedHttpError(view, request, errorResponse)
        }

        override fun onReceivedSslError(view: WebView, handler: SslErrorHandler, error: android.net.http.SslError) {
            handler.cancel()
            showError()
        }

        override fun onReceivedHttpAuthRequest(view: WebView, handler: HttpAuthHandler, host: String, realm: String) {
            handler.cancel()
        }

        override fun onRenderProcessGone(view: WebView, detail: RenderProcessGoneDetail): Boolean {
            if (!isDestroyed) {
                showError()
                Toast.makeText(this@MainActivity, "The movie page restarted safely", Toast.LENGTH_SHORT).show()
            }
            return true
        }
    }

    private inner class DecanChromeClient : WebChromeClient() {
        override fun onCreateWindow(view: WebView, isDialog: Boolean, isUserGesture: Boolean, resultMsg: Message): Boolean {
            val transport = resultMsg.obj as WebView.WebViewTransport
            transport.webView = WebView(this@MainActivity).apply {
                settings.javaScriptEnabled = true
                settings.domStorageEnabled = true
                webViewClient = object : WebViewClient() {
                    override fun shouldOverrideUrlLoading(v: WebView, request: WebResourceRequest): Boolean {
                        return handleUri(request.url)
                    }
                    override fun onPageStarted(v: WebView, url: String, favicon: Bitmap?) {
                        handleUri(Uri.parse(url))
                        v.stopLoading()
                        v.destroy()
                    }
                }
            }
            resultMsg.sendToTarget()
            return true
        }

        override fun onCloseWindow(window: WebView) {
            window.destroy()
        }

        override fun onJsAlert(view: WebView, url: String, message: String, result: JsResult): Boolean {
            AlertDialog.Builder(this@MainActivity)
                .setMessage(message)
                .setPositiveButton(android.R.string.ok) { _, _ -> result.confirm() }
                .setOnCancelListener { result.cancel() }
                .show()
            return true
        }

        override fun onShowFileChooser(
            webView: WebView,
            filePathCallback: ValueCallback<Array<Uri>>,
            fileChooserParams: FileChooserParams
        ): Boolean {
            uploadCallback?.onReceiveValue(null)
            uploadCallback = filePathCallback
            return try {
                val intent = fileChooserParams.createIntent().apply {
                    addCategory(Intent.CATEGORY_OPENABLE)
                }
                startActivityForResult(intent, FILE_CHOOSER_REQUEST)
                true
            } catch (_: ActivityNotFoundException) {
                uploadCallback = null
                false
            }
        }
    }

    private inner class DecanDownloadListener : DownloadListener {
        override fun onDownloadStart(url: String, userAgent: String, contentDisposition: String, mimeType: String, contentLength: Long) {
            if (!isAllowedWebUrl(url) && !url.startsWith("https://")) {
                Toast.makeText(this@MainActivity, "Blocked insecure download", Toast.LENGTH_SHORT).show()
                return
            }
            val fileName = URLUtil.guessFileName(url, contentDisposition, mimeType)
            val request = DownloadManager.Request(Uri.parse(url)).apply {
                setTitle(fileName)
                setDescription("Downloading from Decan Movie")
                setMimeType(mimeType)
                setNotificationVisibility(DownloadManager.Request.VISIBILITY_VISIBLE_NOTIFY_COMPLETED)
                setAllowedOverMetered(true)
                setAllowedOverRoaming(false)
            }
            try {
                val manager = getSystemService(DOWNLOAD_SERVICE) as DownloadManager
                manager.enqueue(request)
                Toast.makeText(this@MainActivity, "Download started", Toast.LENGTH_SHORT).show()
            } catch (_: Exception) {
                Toast.makeText(this@MainActivity, "Download could not start", Toast.LENGTH_SHORT).show()
            }
        }
    }

    @Deprecated("Deprecated in Android SDK; kept for broad device compatibility")
    override fun onActivityResult(requestCode: Int, resultCode: Int, data: Intent?) {
        super.onActivityResult(requestCode, resultCode, data)
        if (requestCode != FILE_CHOOSER_REQUEST) return
        val callback = uploadCallback ?: return
        uploadCallback = null
        val result = if (resultCode == RESULT_OK) {
            WebChromeClient.FileChooserParams.parseResult(resultCode, data)
        } else {
            null
        }
        callback.onReceiveValue(result)
    }

    companion object {
        private const val START_URL = "https://decan-konnect-movie.vercel.app/"
        private const val ALLOWED_HOST = "decan-konnect-movie.vercel.app"
        private const val FILE_CHOOSER_REQUEST = 4101
    }
}

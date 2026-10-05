package id.go.kendarikota.jdih

import android.os.Build
import android.os.Bundle
import android.view.Surface
import android.view.SurfaceHolder
import android.view.SurfaceView
import android.view.View
import android.view.ViewGroup
import io.flutter.embedding.android.FlutterActivity

class MainActivity : FlutterActivity() {
    override fun onCreate(savedInstanceState: Bundle?) {
        super.onCreate(savedInstanceState)
        mintaLajuTertinggi()
    }

    /**
     * Flutter menggambar ke SurfaceView tanpa menyebut laju yang diinginkan,
     * jadi sebagian ponsel (layar 90/120 Hz, layar adaptif, mode hemat daya
     * pabrikan) memilih sendiri dan menahan app di 60 atau bahkan 30 fps.
     * Dua permintaan: mode layar dengan refresh tertinggi, dan suara laju di
     * permukaan Flutter. Suara itu hanya dihitung selama permukaan memang
     * menggambar, jadi layar tetap boleh turun saat app diam.
     */
    private fun mintaLajuTertinggi() {
        @Suppress("DEPRECATION")
        val layar = (if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.R) display
            else windowManager.defaultDisplay) ?: return
        val kini = layar.mode
        val terbaik = layar.supportedModes
            .filter { it.physicalWidth == kini.physicalWidth && it.physicalHeight == kini.physicalHeight }
            .maxByOrNull { it.refreshRate } ?: return
        window.attributes = window.attributes.also { it.preferredDisplayModeId = terbaik.modeId }

        if (Build.VERSION.SDK_INT < Build.VERSION_CODES.R) return
        val permukaan = cariSurfaceView(window.decorView) ?: return
        val suara = { h: SurfaceHolder ->
            if (h.surface.isValid) {
                h.surface.setFrameRate(terbaik.refreshRate, Surface.FRAME_RATE_COMPATIBILITY_DEFAULT)
            }
        }
        suara(permukaan.holder)
        permukaan.holder.addCallback(object : SurfaceHolder.Callback {
            override fun surfaceCreated(h: SurfaceHolder) = suara(h)
            override fun surfaceChanged(h: SurfaceHolder, f: Int, w: Int, t: Int) = suara(h)
            override fun surfaceDestroyed(h: SurfaceHolder) {}
        })
    }

    private fun cariSurfaceView(v: View): SurfaceView? = when (v) {
        is SurfaceView -> v
        is ViewGroup -> (0 until v.childCount).firstNotNullOfOrNull { cariSurfaceView(v.getChildAt(it)) }
        else -> null
    }
}

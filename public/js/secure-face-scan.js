/**
 * Secure Face Scan Session Manager
 * Handles session-based locking for 360° face scans
 */
class SecureFaceScan {
    constructor(scanType = 'registration') {
        this.scanType = scanType;
        this.token = null;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    }

    async start() {
        try {
            const response = await fetch('/api/face-scan/start', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': this.csrfToken,
                    'X-Device-Fingerprint': this.getDeviceFingerprint()
                },
                body: JSON.stringify({ scan_type: this.scanType })
            });
            const data = await response.json();
            if (data.success) {
                this.token = data.token;
                return { success: true, token: data.token, expiresAt: data.expires_at };
            }
            return { success: false, message: data.message || 'Could not start scan session' };
        } catch(e) {
            console.error('Failed to start secure scan:', e);
            return { success: false, message: 'Network error' };
        }
    }

    async updateProgress(directions) {
        if (!this.token) return { success: false, message: 'No active session' };
        try {
            const response = await fetch('/api/face-scan/progress', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': this.csrfToken,
                    'X-Device-Fingerprint': this.getDeviceFingerprint()
                },
                body: JSON.stringify({ token: this.token, directions })
            });
            return await response.json();
        } catch(e) {
            console.error('Progress update failed:', e);
            return { success: false, message: 'Network error' };
        }
    }

    async validateCapture() {
        if (!this.token) return { success: false, message: 'No active session', code: 'NO_SESSION' };
        try {
            const response = await fetch('/api/face-scan/validate-capture', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': this.csrfToken,
                    'X-Device-Fingerprint': this.getDeviceFingerprint()
                },
                body: JSON.stringify({ token: this.token })
            });
            return await response.json();
        } catch(e) {
            console.error('Capture validation failed:', e);
            return { success: false, message: 'Network error', code: 'NETWORK_ERROR' };
        }
    }

    async complete() {
        if (!this.token) return { success: true };
        try {
            const response = await fetch('/api/face-scan/complete', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': this.csrfToken,
                    'X-Device-Fingerprint': this.getDeviceFingerprint()
                },
                body: JSON.stringify({ token: this.token })
            });
            this.token = null;
            return await response.json();
        } catch(e) {
            console.error('Complete scan failed:', e);
            this.token = null;
            return { success: false, message: 'Network error' };
        }
    }

    async abort() {
        if (!this.token) return { success: true };
        try {
            await fetch('/api/face-scan/abort', {
                method: 'POST',
                headers: { 
                    'Content-Type': 'application/json', 
                    'X-CSRF-TOKEN': this.csrfToken,
                    'X-Device-Fingerprint': this.getDeviceFingerprint()
                },
                body: JSON.stringify({ token: this.token })
            });
            this.token = null;
            return { success: true };
        } catch(e) {
            console.error('Abort scan failed:', e);
            this.token = null;
            return { success: false };
        }
    }

    async getStatus() {
        try {
            const response = await fetch('/api/face-scan/status', {
                headers: { 
                    'X-Device-Fingerprint': this.getDeviceFingerprint()
                }
            });
            return await response.json();
        } catch(e) {
            return { active: false };
        }
    }

    getDeviceFingerprint() {
        // Simple device fingerprint based on browser properties
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        ctx.textBaseline = 'top';
        ctx.font = '14px Arial';
        ctx.fillText('fingerprint', 2, 2);
        
        const data = [
            navigator.userAgent,
            navigator.language,
            screen.width + 'x' + screen.height,
            new Date().getTimezoneOffset(),
            canvas.toDataURL()
        ].join('|');
        
        // Simple hash
        let hash = 0;
        for (let i = 0; i < data.length; i++) {
            const char = data.charCodeAt(i);
            hash = ((hash << 5) - hash) + char;
            hash = hash & hash;
        }
        return hash.toString(36);
    }

    isActive() {
        return this.token !== null;
    }
}

// Export for use
window.SecureFaceScan = SecureFaceScan;

// AI SEARCH SYSTEM - FIXED VERSION
// Menggunakan IIFE pattern untuk mencegah redeclaration

(function() {
    'use strict';
    
    class AiSearchSystem {
        constructor() {
            // Pastikan modal ada sebelum inisialisasi
            this.modal = document.getElementById('aiSearchModal');
            if (!this.modal) {
                console.warn('AI Search Modal not found');
                return;
            }
            
            // Inisialisasi elemen
            this.resultsContainer = document.getElementById('aiResults');
            this.queryText = document.getElementById('aiQueryText');
            this.explanationContent = document.getElementById('aiExplanationContent');
            this.aiSearchInput = document.getElementById('aiSearchInput');
            this.performAiSearchBtn = document.getElementById('performAiSearch');
            this.closeAiModalBtn = document.getElementById('closeAiModal');
            
            // Bind methods untuk event listeners
            this.handleEnterKey = this.handleEnterKey.bind(this);
            this.handleSearchClick = this.handleSearchClick.bind(this);
            this.handleCloseClick = this.handleCloseClick.bind(this);
            this.handleOutsideClick = this.handleOutsideClick.bind(this);
            
            this.init();
        }
        
        init() {
            this.setupEventListeners();
        }
        
        setupEventListeners() {
            if (!this.modal) return;
            
            // Enter key untuk AI search
            if (this.aiSearchInput) {
                this.aiSearchInput.addEventListener('keypress', this.handleEnterKey);
            }
            
            // Tombol Tanya AI
            if (this.performAiSearchBtn) {
                this.performAiSearchBtn.addEventListener('click', this.handleSearchClick);
            }
            
            // Close modal
            if (this.closeAiModalBtn) {
                this.closeAiSearchBtn.addEventListener('click', this.handleCloseClick);
            }
            
            // Close modal saat klik di luar
            this.modal.addEventListener('click', this.handleOutsideClick);
            
            // Tambahkan event listener untuk escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.modal.classList.contains('show')) {
                    this.closeModal();
                }
            });
        }
        
        handleEnterKey(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.performAiSearch();
            }
        }
        
        handleSearchClick() {
            this.performAiSearch();
        }
        
        handleCloseClick() {
            this.closeModal();
        }
        
        handleOutsideClick(e) {
            if (e.target === this.modal) {
                this.closeModal();
            }
        }
        
        cleanupEventListeners() {
            if (this.aiSearchInput) {
                this.aiSearchInput.removeEventListener('keypress', this.handleEnterKey);
            }
            
            if (this.performAiSearchBtn) {
                this.performAiSearchBtn.removeEventListener('click', this.handleSearchClick);
            }
            
            if (this.closeAiModalBtn) {
                this.closeAiModalBtn.removeEventListener('click', this.handleCloseClick);
            }
            
            if (this.modal) {
                this.modal.removeEventListener('click', this.handleOutsideClick);
            }
        }
        
        async performAiSearch() {
            const query = this.aiSearchInput ? this.aiSearchInput.value.trim() : '';
            if (!query) {
                if (this.aiSearchInput) {
            this.aiSearchInput.focus();
            this.aiSearchInput.placeholder = "Masukkan pertanyaan terlebih dahulu...";
            // Atau bisa tambahkan class CSS untuk styling
            this.aiSearchInput.classList.add('border-red-500', 'placeholder-red-400');
            setTimeout(() => {
                this.aiSearchInput.classList.remove('border-red-500', 'placeholder-red-400');
                this.aiSearchInput.placeholder = "Contoh: 'Cari peraturan tentang hukum di Kota Kendari'";
            }, 3000);
        }
                return;
            }
            
            // Clear input setelah search
            if (this.aiSearchInput) {
                this.aiSearchInput.value = '';
            }
            
            // Tampilkan loading
            if (this.queryText) {
                this.queryText.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-robot text-purple-600 mr-2"></i>
                        <span class="font-medium">AI sedang menganalisis:</span>
                        <span class="ml-2 text-purple-700">"${query}"</span>
                    </div>
                `;
            }
            
            if (this.explanationContent) {
                this.explanationContent.innerHTML = `
                    <div class="flex items-center justify-center py-4">
                        <i class="fas fa-spinner fa-spin text-blue-500 mr-3"></i>
                        <div>
                            <div class="font-medium text-gray-700">AI sedang memproses...</div>
                            <div class="text-xs text-gray-500 mt-1">Mengambil data dan menganalisis pertanyaan Anda</div>
                        </div>
                    </div>
                `;
            }
            
            if (this.resultsContainer) {
                this.resultsContainer.innerHTML = `
                    <div class="ai-result-item">
                        <div class="flex items-center justify-center py-6">
                            <i class="fas fa-spinner fa-spin text-purple-600 mr-3 text-xl"></i>
                            <div>
                                <div class="font-medium text-gray-700">Mencari dokumen relevan...</div>
                                <div class="text-sm text-gray-500 mt-1">Mencocokkan dengan database dokumen hukum</div>
                            </div>
                        </div>
                    </div>
                `;
            }
            
            this.openModal();
            
            try {
                // Dapatkan CSRF token
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                if (!csrfToken) {
                    throw new Error('CSRF token not found');
                }
                
                // Panggil API backend
                const response = await fetch('/ai/search', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ query: query })
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                this.displayAiResults(query, data);
                
            } catch (error) {
                console.error('AI Search error:', error);
                this.displayFallbackResults(query);
            }
        }
        
        displayAiResults(query, data) {
            // Update query text
            if (this.queryText) {
                this.queryText.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-robot text-purple-600 mr-2"></i>
                        <span class="font-medium">Hasil untuk:</span>
                        <span class="ml-2 text-purple-700 font-semibold">"${query}"</span>
                        <span class="ml-3 text-sm text-gray-500">(${data.total || (data.documents ? data.documents.length : 0)} dokumen ditemukan)</span>
                    </div>
                `;
            }
            
            // Update explanation
            if (this.explanationContent) {
                const explanation = data.explanation || 'Analisis berdasarkan dokumen yang relevan.';
                this.explanationContent.innerHTML = `
                    <p class="text-gray-700">${explanation}</p>
                    <p class="mt-2 text-gray-600 text-sm">Berdasarkan analisis AI terhadap database dokumen hukum Kota Kendari.</p>
                `;
            }
            
            // Update document results
            if (this.resultsContainer) {
                let html = '';
                
                if (data.documents && data.documents.length > 0) {
                    data.documents.forEach((doc, index) => {
                        // Determine accuracy color
                        let accuracyClass = 'accuracy-badge';
                        if (doc.accuracy >= 90) {
                            accuracyClass += ' green';
                        } else if (doc.accuracy >= 80) {
                            accuracyClass += ' yellow';
                        } else if (doc.accuracy >= 70) {
                            accuracyClass += ' orange';
                        } else {
                            accuracyClass += ' red';
                        }
                        
                        html += `
                            <div class="ai-result-item">
                                <div class="flex justify-between items-start mb-2">
                                    <div>
                                        <h5 class="font-semibold text-gray-800 text-lg">${doc.title || 'Tanpa Judul'}</h5>
                                        <div class="flex flex-wrap items-center gap-2 mt-2">
                                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded-full">
                                                ${doc.type || 'Dokumen'}
                                            </span>
                                            ${doc.year ? `
                                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded-full">
                                                Tahun ${doc.year}
                                            </span>
                                            ` : ''}
                                            ${doc.category ? `
                                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">
                                                ${doc.category}
                                            </span>
                                            ` : ''}
                                            ${doc.accuracy ? `
                                            <span class="${accuracyClass}">
                                                <i class="fas fa-chart-line mr-1"></i>
                                                ${doc.accuracy}% Relevan
                                            </span>
                                            ` : ''}
                                        </div>
                                    </div>
                                </div>
                                ${doc.description ? `
                                <p class="text-gray-600 text-sm mb-3">${doc.description}</p>
                                ` : ''}
                                <div class="flex gap-2">
                                    ${doc.url ? `
                                    <a href="${doc.url}" 
                                       class="px-4 py-2 bg-purple-600 text-white text-sm rounded-lg hover:bg-purple-700 transition flex items-center gap-1">
                                        <i class="fas fa-eye"></i>
                                        Lihat Dokumen
                                    </a>
                                    ` : ''}
                                    ${doc.download_url ? `
                                    <a href="${doc.download_url}" 
                                       class="px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition flex items-center gap-1">
                                        <i class="fas fa-download"></i>
                                        Download
                                    </a>
                                    ` : ''}
                                </div>
                            </div>
                        `;
                    });
                } else {
                    html = `
                        <div class="ai-result-item">
                            <div class="text-center py-6">
                                <i class="fas fa-search text-gray-400 text-3xl mb-3"></i>
                                <p class="text-gray-600 font-medium">Tidak ditemukan dokumen yang relevan</p>
                                <p class="text-gray-500 text-sm mt-1">Coba gunakan kata kunci yang berbeda atau lebih spesifik</p>
                            </div>
                        </div>
                    `;
                }
                
                this.resultsContainer.innerHTML = html;
            }
        }
        
        displayFallbackResults(query) {
            if (this.queryText) {
                this.queryText.innerHTML = `
                    <div class="flex items-center">
                        <i class="fas fa-robot text-purple-600 mr-2"></i>
                        <span class="font-medium">Hasil untuk:</span>
                        <span class="ml-2 text-purple-700 font-semibold">"${query}"</span>
                    </div>
                `;
            }
            
            if (this.explanationContent) {
                this.explanationContent.innerHTML = `
                    <p class="text-yellow-600">Terjadi kesalahan dalam memproses permintaan AI. Silakan coba lagi.</p>
                `;
            }
            
            if (this.resultsContainer) {
                this.resultsContainer.innerHTML = `
                    <div class="ai-result-item">
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-triangle text-yellow-500 text-2xl mb-2"></i>
                            <p class="text-gray-600">Koneksi terputus. Silakan coba lagi.</p>
                        </div>
                    </div>
                `;
            }
        }
        
        openModal() {
            if (this.modal) {
                this.modal.classList.add('show');
                document.body.style.overflow = 'hidden';
                
                // Fokus ke input saat modal terbuka
                setTimeout(() => {
                    if (this.aiSearchInput) {
                        this.aiSearchInput.focus();
                    }
                }, 100);
            }
        }
        
        closeModal() {
            if (this.modal) {
                this.modal.classList.remove('show');
                document.body.style.overflow = '';
            }
        }
        
        destroy() {
            this.cleanupEventListeners();
            console.log('AiSearchSystem instance destroyed');
        }
    }
    
    // Singleton Manager untuk mengelola instance
    window.AiSearchManager = {
        instance: null,
        
        getInstance() {
            // Cek apakah modal ada di DOM
            if (!document.getElementById('aiSearchModal')) {
                return null;
            }
            
            if (!this.instance) {
                this.instance = new AiSearchSystem();
            }
            return this.instance;
        },
        
        destroyInstance() {
            if (this.instance) {
                this.instance.destroy();
                this.instance = null;
            }
        },
        
        initializeIfNeeded() {
            if (document.getElementById('aiSearchModal') && !this.instance) {
                this.instance = new AiSearchSystem();
            }
        }
    };
    
    // Auto-initialize saat DOM siap
    document.addEventListener('DOMContentLoaded', function() {
        AiSearchManager.initializeIfNeeded();
    });
    
    // Handle Livewire navigation
    if (typeof Livewire !== 'undefined') {
        // Destroy instance sebelum navigasi
        document.addEventListener('livewire:navigating', function() {
            AiSearchManager.destroyInstance();
        });
        
        // Re-initialize setelah navigasi
        document.addEventListener('livewire:navigated', function() {
            setTimeout(() => {
                AiSearchManager.initializeIfNeeded();
            }, 50);
        });
    }
    
    // Global helper function untuk membuka modal dari mana saja
    window.openAiSearchModal = function() {
        const instance = AiSearchManager.getInstance();
        if (instance) {
            instance.openModal();
            return true;
        }
        
        // Jika modal belum ada, tunggu dan coba lagi
        console.warn('AI Search modal not found, trying to initialize...');
        AiSearchManager.initializeIfNeeded();
        
        setTimeout(() => {
            const retryInstance = AiSearchManager.getInstance();
            if (retryInstance) {
                retryInstance.openModal();
            } else {
                console.error('Cannot open AI Search modal');
            }
        }, 100);
        
        return false;
    };
    
    // Ekspos class untuk pengujian (opsional)
    window.AiSearchSystem = AiSearchSystem;
    
})(); // End IIFE

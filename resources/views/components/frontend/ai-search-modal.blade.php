<!-- AI SEARCH MODAL -->
<div id="aiSearchModal" class="ai-search-modal">
    <div class="ai-search-container">
        <div class="ai-search-header">
            <div class="ai-search-modal-title">
                <i class="fas fa-robot text-purple-600"></i>
                Hasil Pencarian AI
            </div>
            <button id="closeAiModal" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <div id="aiQueryText" class="text-gray-600 mb-4 p-3 bg-gray-50 rounded-lg">
            <!-- Pertanyaan akan dimuat di sini -->
        </div>
        
        <!-- AI Explanation -->
        <div id="aiExplanation" class="mb-6 p-4 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg border border-blue-200">
            <div class="flex items-start mb-2">
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-2 rounded-lg mr-3">
                    <i class="fas fa-brain text-lg"></i>
                </div>
                <div>
                    <h4 class="font-semibold text-lg text-blue-800 mb-1">Penjelasan AI</h4>
                    <div id="aiExplanationContent" class="text-sm text-gray-700">
                        <!-- Penjelasan dari AI akan dimuat di sini -->
                    </div>
                </div>
            </div>
            <div class="flex items-center text-xs text-gray-500 mt-2">
                <i class="fas fa-info-circle mr-1"></i>
                <span>Dijelaskan oleh Google Gemini AI</span>
            </div>
        </div>
        
        <!-- Document Results -->
        <h4 class="text-lg font-semibold mb-4 text-gray-800">Dokumen Relevan:</h4>
        <div id="aiResults" class="ai-results">
            <!-- Hasil pencarian AI akan muncul di sini -->
        </div>
    </div>
</div>
// ignore: unused_import
import 'package:intl/intl.dart' as intl;

import 'app_localizations.dart';

// ignore_for_file: type=lint

/// The translations for English (`en`).
class AppLocalizationsEn extends AppLocalizations {
  AppLocalizationsEn([String locale = 'en']) : super(locale);

  @override
  String get appName => 'JDIH Kendari City';

  @override
  String get navHome => 'Home';

  @override
  String get navDocuments => 'Documents';

  @override
  String get navAskAi => 'Ask AI';

  @override
  String get navNews => 'News';

  @override
  String get navMenu => 'Menu';

  @override
  String aiAndSearch(String label) {
    return '$label and search';
  }

  @override
  String get errServer =>
      'The server is having problems. Please try again shortly.';

  @override
  String get errUnknownReply =>
      'The server sent an unrecognized response. If you are on public Wi-Fi, make sure you have signed in to the network.';

  @override
  String get errTooMany =>
      'Too many requests in a short time. Wait a moment, then try again.';

  @override
  String get errInvalidInput =>
      'Some fields are incomplete or invalid. Check them, then submit again.';

  @override
  String get errNotFound => 'The requested data was not found.';

  @override
  String errRequestFailed(int code) {
    return 'The request could not be processed ($code). Please try again.';
  }

  @override
  String get errTimeout => 'The server is slow to respond. Please try again.';

  @override
  String get errOffline =>
      'Cannot connect to the JDIH server. Check your internet connection, then try again.';

  @override
  String minRead(int n) {
    return '$n min read';
  }

  @override
  String get linkOpenFailed => 'Could not open the link.';

  @override
  String get closeImage => 'Close image';

  @override
  String get statusInForce => 'In force';

  @override
  String get statusNotInForce => 'Not in force';

  @override
  String get statusRevoked => 'Revoked';

  @override
  String get statusAmended => 'Amended';

  @override
  String get legalDocument => 'Regulation';

  @override
  String get seeAll => 'See all';

  @override
  String get language => 'Language';

  @override
  String get notFound => 'Nothing found';

  @override
  String noMatch(String query) {
    return 'Nothing matches \"$query\".';
  }

  @override
  String get noMatchHint =>
      'Try a shorter keyword, check the spelling, or change the category.';

  @override
  String get aiPromoBody =>
      'Ask in everyday language. Answers come as a conversation, complete with the relevant legal documents.';

  @override
  String get aiPromoStart => 'Start asking';

  @override
  String get latest => 'Latest';

  @override
  String get share => 'Share';

  @override
  String get readLess => 'Show less';

  @override
  String get readMore => 'Read more';

  @override
  String get retry => 'Try again';

  @override
  String get noData => 'No data.';

  @override
  String get emptyTitle => 'Nothing here yet';

  @override
  String get reload => 'Reload';

  @override
  String get loadMore => 'Load more';

  @override
  String get catRegulations => 'Regulations & Decrees';

  @override
  String get catRegulationsShort => 'Regulations';

  @override
  String get catRegulationsSub =>
      'Regional and mayoral regulations, mayoral decrees';

  @override
  String get catMonographs => 'Legal Monographs';

  @override
  String get catMonographsShort => 'Monographs';

  @override
  String get catMonographsSub => 'Books and studies on regional law';

  @override
  String get catArticles => 'Legal Articles & Magazines';

  @override
  String get catArticlesShort => 'Articles';

  @override
  String get catArticlesSub => 'Legal articles and magazines';

  @override
  String get catRulings => 'Court Rulings';

  @override
  String get catRulingsShort => 'Rulings';

  @override
  String get catRulingsSub => 'Court rulings concerning the region';

  @override
  String get latestDocuments => 'Latest Documents';

  @override
  String get jdihNews => 'JDIH News';

  @override
  String get collectionStats => 'Collection Statistics';

  @override
  String get greetMorning => 'Good morning';

  @override
  String get greetMidday => 'Good day';

  @override
  String get greetAfternoon => 'Good afternoon';

  @override
  String get greetEvening => 'Good evening';

  @override
  String get logoJdihn => 'JDIHN logo';

  @override
  String get homeHeadline => 'What regulation are you looking for today?';

  @override
  String get adatMeaning => 'Whoever honors custom will be honored';

  @override
  String get homeSearchHint => 'Title, number, or a question...';

  @override
  String get search => 'Search';

  @override
  String get noAbbr => 'No.';

  @override
  String get views => 'views';

  @override
  String get downloads => 'downloads';

  @override
  String get pickDocument => 'Select a document';

  @override
  String get pickDocumentHint =>
      'Tap a document in the list to read its details here, side by side with the other results.';

  @override
  String get legalDocuments => 'Legal Documents';

  @override
  String get latestLegalProducts => 'Latest Legal Products';

  @override
  String docsAvailable(int count) {
    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '$count documents available',
      one: '1 document available',
    );
    return '$_temp0';
  }

  @override
  String get filterAllTypes => 'All types';

  @override
  String get filterAllYears => 'All years';

  @override
  String get filterAllStatus => 'All statuses';

  @override
  String get type => 'Type';

  @override
  String get year => 'Year';

  @override
  String get status => 'Status';

  @override
  String get number => 'Number';

  @override
  String get searchTitleHint => 'Search titles...';

  @override
  String get reset => 'Reset';

  @override
  String docCount(int count) {
    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '$count documents',
      one: '1 document',
    );
    return '$_temp0';
  }

  @override
  String docCountFiltered(int count) {
    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '$count documents match the filters',
      one: '1 document matches the filters',
    );
    return '$_temp0';
  }

  @override
  String get emptyCategory => 'No documents in this category yet.';

  @override
  String get loosenFilters =>
      'Loosen the filters or check the spelling of your keyword.';

  @override
  String get resetFilter => 'Reset filters';

  @override
  String get back => 'Back';

  @override
  String numberValue(String nomor) {
    return 'No. $nomor';
  }

  @override
  String yearValue(String tahun) {
    return 'Year $tahun';
  }

  @override
  String get tabAbout => 'About';

  @override
  String tabFiles(int count) {
    return 'Files ($count)';
  }

  @override
  String get tabRelated => 'Related';

  @override
  String get tabImplementing => 'Implementing';

  @override
  String get aboutDocument => 'About this Document';

  @override
  String get abstract => 'Abstract';

  @override
  String abstractOf(String judul) {
    return 'Abstract of $judul';
  }

  @override
  String fileAbstract(String judul) {
    return 'abstract $judul';
  }

  @override
  String get metaForm => 'Form';

  @override
  String get metaPlacePublished => 'Place of publication';

  @override
  String get publisher => 'Publisher';

  @override
  String get metaDateEnacted => 'Date enacted';

  @override
  String get metaDatePromulgated => 'Date promulgated';

  @override
  String get source => 'Source';

  @override
  String get metaLegalField => 'Field of law';

  @override
  String get metaSignatory => 'Signatory';

  @override
  String get metaPhysicalDesc => 'Physical description';

  @override
  String get metaCourt => 'Court';

  @override
  String get metaPetitioner => 'Petitioner';

  @override
  String get metaRespondent => 'Respondent';

  @override
  String get metaCaseType => 'Case type';

  @override
  String get metaTeu => 'Main entry';

  @override
  String get subject => 'Subjects';

  @override
  String get author => 'Authors';

  @override
  String get noFiles => 'No files yet';

  @override
  String get noFilesHint =>
      'This document has no files to view or download yet. Try checking the related regulations.';

  @override
  String get document => 'Document';

  @override
  String fileAttachmentN(String judul, int n) {
    return '$judul attachment $n';
  }

  @override
  String get noRelated => 'No related regulations';

  @override
  String get noRelatedHint =>
      'This document stands alone: it does not amend or revoke any other regulation, and no other regulation amends it.';

  @override
  String get noImplementing => 'No implementing regulations';

  @override
  String get noImplementingHint =>
      'Regulations issued to implement this one will appear here.';

  @override
  String get statistics => 'Statistics';

  @override
  String viewsDownloads(String views, String downloads) {
    return '$views views · $downloads downloads';
  }

  @override
  String get fileUnavailable => 'File not available';

  @override
  String get viewDocument => 'View Document';

  @override
  String get downloadDocument => 'Download Document';

  @override
  String get fileKindPdf => 'PDF document · readable in the app';

  @override
  String fileKindOther(String ext) {
    return '$ext file · download to open';
  }

  @override
  String get fileGeneric => 'File';

  @override
  String downloading(String name) {
    return 'Downloading $name...';
  }

  @override
  String get downloadFailed =>
      'The file could not be downloaded. Check your connection and storage space, then try again.';

  @override
  String get goToPage => 'Go to page';

  @override
  String get pageNumber => 'Page number';

  @override
  String get cancel => 'Cancel';

  @override
  String get go => 'Go';

  @override
  String pageOutOfRange(int total) {
    return 'This document only has pages 1 to $total.';
  }

  @override
  String get download => 'Download';

  @override
  String get pdfCannotDisplay =>
      'This document cannot be displayed in the app, but you can still download it and open it in a PDF reader.';

  @override
  String get downloadInstead => 'Download instead';

  @override
  String get prevPage => 'Previous page';

  @override
  String get nextPage => 'Next page';

  @override
  String pageOf(int page, int total) {
    return 'Page $page of $total';
  }

  @override
  String get view => 'View';

  @override
  String get newsAndInfo => 'News & Information';

  @override
  String get news => 'News';

  @override
  String get announcements => 'Announcements';

  @override
  String get announcement => 'Announcement';

  @override
  String get video => 'Video';

  @override
  String get legalInfoShort => 'Legal Info';

  @override
  String get legalInfo => 'Legal Information';

  @override
  String get clearSearch => 'Clear search';

  @override
  String get noMatchHintShort =>
      'Try a shorter keyword, or check the spelling.';

  @override
  String get searchNewsHint => 'Search news titles...';

  @override
  String get emptyNews => 'No news has been published yet.';

  @override
  String get searchAnnouncementsHint => 'Search announcement titles...';

  @override
  String get emptyAnnouncements => 'No announcements yet.';

  @override
  String get emptyVideos => 'No videos on the JDIH channel yet.';

  @override
  String get moreNews => 'More News';

  @override
  String get moreAnnouncements => 'More Announcements';

  @override
  String get moreVideos => 'More Videos';

  @override
  String get enlargeCover => 'Enlarge cover image';

  @override
  String get tapToEnlarge => 'Tap to enlarge';

  @override
  String get attachment => 'Attachment';

  @override
  String get announcementAttachment => 'Announcement attachment';

  @override
  String fileAttachmentOf(String judul) {
    return 'attachment $judul';
  }

  @override
  String fileDocumentOf(String judul) {
    return 'document $judul';
  }

  @override
  String get all => 'All';

  @override
  String get emptyLegalInfo =>
      'Academic papers, draft regulations and legal studies will appear here once published.';

  @override
  String get emptyCategoryTitle => 'This category is empty';

  @override
  String get emptySelectedCategory =>
      'There are no documents in the selected category yet.';

  @override
  String emptyTypeNamed(String jenis) {
    return 'There are no $jenis documents yet. Other categories may have some.';
  }

  @override
  String get seeAllCategories => 'See all categories';

  @override
  String get linkCopied => 'Video link copied.';

  @override
  String get videoNotPlayable =>
      'This video is hosted outside YouTube and cannot be played in the app yet.';

  @override
  String get openInYoutube => 'Open in YouTube';

  @override
  String get openVideoLink => 'Open video link';

  @override
  String get copy => 'Copy';

  @override
  String get profileHistory => 'Brief History';

  @override
  String get profileLegalBasis => 'Legal Basis';

  @override
  String get profileVision => 'Vision';

  @override
  String get profileMission => 'Mission';

  @override
  String get profileStructure => 'Organizational Structure';

  @override
  String get more => 'More';

  @override
  String get jdihProfile => 'JDIH Profile';

  @override
  String get disabilityServices => 'Disability Services';

  @override
  String get lawMaking => 'Law-Making';

  @override
  String get satisfactionSurvey => 'Satisfaction Survey';

  @override
  String get aboutJdih => 'About JDIH';

  @override
  String get menuServices => 'Services & Information';

  @override
  String get menuApp => 'App';

  @override
  String get orgStructureOnWeb =>
      'The organizational structure is available on the JDIH Kendari City website.';

  @override
  String orgChart(int n) {
    return 'Organizational Chart ($n)';
  }

  @override
  String get jdihFull => 'Legal Documentation and Information Network';

  @override
  String get phone => 'Phone';

  @override
  String get email => 'Email';

  @override
  String get address => 'Address';

  @override
  String get socialMedia => 'Social Media';

  @override
  String get appLabel => 'JDIH Kendari City app';

  @override
  String loadingApp(String app) {
    return '$app, loading';
  }

  @override
  String get kendariGov => 'Kendari City Government';

  @override
  String get puuAcademicPaper => 'Academic Paper';

  @override
  String get puuExplanatory => 'Explanatory Memorandum';

  @override
  String get puuDraft => 'Draft Legislation';

  @override
  String get puuResearch => 'Legal Research';

  @override
  String get puuLegalReview => 'Legal Review';

  @override
  String get puuConstitutionalReview => 'Constitutional Review';

  @override
  String get puuAnalysis => 'Analysis & Evaluation';

  @override
  String get secBackground => 'Background';

  @override
  String get secProblem => 'Problem Statement';

  @override
  String get secObjective => 'Research Objectives';

  @override
  String get secMethod => 'Methodology';

  @override
  String get secFocus => 'Research Focus';

  @override
  String get secResults => 'Research Findings';

  @override
  String get secReviewObject => 'Object of Review';

  @override
  String get secReviewConclusion => 'Review Conclusions';

  @override
  String get secConstitutional => 'Constitutional Aspects';

  @override
  String get secEvalFindings => 'Evaluation Findings';

  @override
  String get secRecommendation => 'Recommendations';

  @override
  String get secImprovement => 'Recommended Improvements';

  @override
  String get secNotes => 'Notes';

  @override
  String get metaInitiator => 'Initiating institution';

  @override
  String get metaStage => 'Stage';

  @override
  String get metaWriter => 'Writer';

  @override
  String get metaEditor => 'Editor';

  @override
  String get metaKeywords => 'Keywords';

  @override
  String get metaViews => 'Views';

  @override
  String get mainDocument => 'Main document';

  @override
  String timesCount(int n) {
    return '$n';
  }

  @override
  String get searchDisabilityHint => 'Search disability documents...';

  @override
  String get metaPlaceEnacted => 'Place of enactment';

  @override
  String get metaEnactedBy => 'Enacted by';

  @override
  String get metaDisabilityType => 'Type of disability';

  @override
  String get metaScope => 'Scope';

  @override
  String get metaPolicySector => 'Policy sector';

  @override
  String get metaPages => 'Number of pages';

  @override
  String get smartSearch => 'Smart Search';

  @override
  String get textSearch => 'Text Search';

  @override
  String get searchDocsHint => 'Search by title, number, or topic...';

  @override
  String resultsFor(int count, String query) {
    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '$count results for \"$query\"',
      one: '1 result for \"$query\"',
    );
    return '$_temp0';
  }

  @override
  String get noMatchHintAi =>
      'Try a shorter keyword, change the category above, or ask the AI directly.';

  @override
  String get searchLegalProducts => 'Search Legal Products';

  @override
  String get searchIntro =>
      'Type a regulation title, number, or topic, then press search on the keyboard. The categories above narrow the results.';

  @override
  String get aiSuggest1 => 'Waste levies';

  @override
  String get aiSuggest1Ask => 'What are the rules on waste collection levies?';

  @override
  String get aiSuggest2 => 'Latest mayoral regulation';

  @override
  String get aiSuggest2Ask => 'What is the latest mayoral regulation about?';

  @override
  String get aiSuggest3 => 'Building permits (IMB/PBG)';

  @override
  String get aiSuggest3Ask => 'How do I apply for a building permit (IMB/PBG)?';

  @override
  String get aiSuggest4 => 'Regional taxes';

  @override
  String get aiSuggest4Ask =>
      'What types of regional taxes does Kendari City have?';

  @override
  String get aiSuggest5 => 'Regional budget (APBD)';

  @override
  String get aiSuggest5Ask =>
      'What does the latest regional regulation on the APBD (regional budget) contain?';

  @override
  String get aiSuggest6 => 'Disability rights';

  @override
  String get aiSuggest6Ask =>
      'What are the rules on the rights of persons with disabilities?';

  @override
  String get aiNoAnswer => 'Sorry, I can\'t answer that question yet.';

  @override
  String get chatCleared => 'Conversation cleared.';

  @override
  String get undo => 'Undo';

  @override
  String get clearChat => 'Clear conversation';

  @override
  String get chatLimitReached => 'Type to start a new conversation';

  @override
  String get askHint => 'Type your question...';

  @override
  String get send => 'Send';

  @override
  String get aiDisclaimer =>
      'Answers are generated automatically by AI. Always check the original documents.';

  @override
  String get jdihAssistant => 'JDIH Assistant';

  @override
  String get aiGreeting =>
      'Hello! Ask me anything about the laws and regulations of Kendari City. I will answer and show you the related JDIH documents.';

  @override
  String get tryAsking => 'Try asking';

  @override
  String get resend => 'Resend';

  @override
  String get aiStepUnderstand => 'Understanding the question';

  @override
  String get aiStepSearch => 'Searching JDIH documents';

  @override
  String get aiStepMatch => 'Matching relevant articles';

  @override
  String get aiStepCompose => 'Composing the answer';

  @override
  String get aiSearching => 'Searching documents';

  @override
  String get relatedDocs => 'Related documents';

  @override
  String docsAttached(int count) {
    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '$count documents attached',
      one: '1 document attached',
    );
    return '$_temp0';
  }

  @override
  String percentRelevant(int pct) {
    return '$pct% relevant';
  }

  @override
  String chatFull(int max) {
    return 'This conversation has reached $max questions. Your next question will start a new conversation.';
  }

  @override
  String get newChat => 'New conversation';

  @override
  String updatedAt(String date) {
    return 'Updated $date.';
  }

  @override
  String get statsSource =>
      'Figures are computed directly from the JDIH Kendari City database.';

  @override
  String get totalLegalDocs => 'Total legal documents';

  @override
  String get metaDownloads => 'Downloads';

  @override
  String get docsPerYear => 'Documents per year';

  @override
  String get docsPerYearSub => 'Number of documents by year of issue';

  @override
  String labelDocs(String label, int count) {
    final intl.NumberFormat countNumberFormat =
        intl.NumberFormat.decimalPattern(localeName);
    final String countString = countNumberFormat.format(count);

    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '$countString documents',
      one: '1 document',
    );
    return '$label: $_temp0';
  }

  @override
  String otherTypes(int n) {
    return 'Others ($n types)';
  }

  @override
  String get docsPerType => 'Documents by type';

  @override
  String get docsPerTypeSub => 'The seven types with the largest collections';

  @override
  String get statusTitle => 'Legal status';

  @override
  String get statusSub => 'Share of the collection still in force';

  @override
  String get statusNoData => 'Status data is not available yet.';

  @override
  String labelDocsPct(String label, int count, String pct) {
    final intl.NumberFormat countNumberFormat =
        intl.NumberFormat.decimalPattern(localeName);
    final String countString = countNumberFormat.format(count);

    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '$countString documents',
      one: '1 document',
    );
    return '$label: $_temp0, $pct';
  }

  @override
  String get mostViewed => 'Most viewed';

  @override
  String get mostViewedSub => 'The five documents with the most readers';

  @override
  String get mostViewedEmpty =>
      'No document reads have been recorded yet. This fills in once visitors open document pages.';

  @override
  String viewedTimes(int count) {
    final intl.NumberFormat countNumberFormat =
        intl.NumberFormat.decimalPattern(localeName);
    final String countString = countNumberFormat.format(count);

    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: '$countString views',
      one: '1 view',
    );
    return '$_temp0';
  }

  @override
  String get userStudent => 'Student';

  @override
  String get userAcademic => 'Academic';

  @override
  String get userPractitioner => 'Legal practitioner';

  @override
  String get userPublic => 'General public';

  @override
  String get userOther => 'Other';

  @override
  String get aspectAccess => 'Ease of access';

  @override
  String get aspectCompleteness => 'Completeness of information';

  @override
  String get aspectSpeed => 'Loading speed';

  @override
  String get aspectInterface => 'Interface design';

  @override
  String get aspectRelevance => 'Search relevance';

  @override
  String get errNameRequired => 'Name is required.';

  @override
  String get errEmailFormat => 'Invalid email format, e.g. name@example.com';

  @override
  String get errPickUserType => 'Choose a user type.';

  @override
  String get errRateAll => 'Rate every aspect.';

  @override
  String get thankYou => 'Thank you!';

  @override
  String get surveySent => 'Your survey has been submitted.';

  @override
  String get ok => 'OK';

  @override
  String get fieldName => 'Name';

  @override
  String get fieldInstitution => 'Institution';

  @override
  String get fieldUserType => 'User type';

  @override
  String get fieldRating => 'Rate our service';

  @override
  String get fieldSuggestions => 'Suggestions for improvement';

  @override
  String get fieldFeatures => 'Features you would like';

  @override
  String get fieldContactOk => 'Willing to be contacted';

  @override
  String get fieldContact => 'Contact (phone/WhatsApp)';

  @override
  String get submitSurvey => 'Submit Survey';

  @override
  String get surveyHeading => 'Help us serve you better';

  @override
  String get surveyIntro =>
      'Your rating helps us improve JDIH services. Fields marked Optional can be skipped.';

  @override
  String get surveyAbout => 'About you';

  @override
  String get surveyFeedback => 'Feedback';

  @override
  String get optional => 'Optional';

  @override
  String ratingWord(String n) {
    String _temp0 = intl.Intl.selectLogic(n, {
      '1': 'Very poor',
      '2': 'Poor',
      '3': 'Fair',
      '4': 'Good',
      'other': 'Very good',
    });
    return '$_temp0';
  }

  @override
  String surveyProgress(int done, int total) {
    return '$done of $total required';
  }

  @override
  String ratingTooltip(String label, int n) {
    return '$label, $n of 5';
  }

  @override
  String get logoKendari => 'Kendari City Government logo';

  @override
  String get statsDocsLabel => 'legal documents on record';

  @override
  String statsReach(String views, String downloads) {
    return 'Viewed $views times, downloaded $downloads times.';
  }

  @override
  String get statsLensYear => 'Year';

  @override
  String get statsLensType => 'Type';

  @override
  String get statsLensStatus => 'Status';

  @override
  String statsYearDocs(int count, String year) {
    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: 'documents',
      one: 'document',
    );
    return '$_temp0 issued in $year';
  }

  @override
  String statsYearSoFar(int count, String year) {
    String _temp0 = intl.Intl.pluralLogic(
      count,
      locale: localeName,
      other: 'documents',
      one: 'document',
    );
    return '$_temp0 issued in $year so far';
  }

  @override
  String get statsYearHint => 'Drag to pick a year';

  @override
  String get statsInForceShare => 'of the collection still in force';
}

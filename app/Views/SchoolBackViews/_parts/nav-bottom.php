
     
        </div>
        <div class="footer <?=(isset($pageTitleHideOnPrint) AND $pageTitleHideOnPrint) ? 'd-print-none' : ''; ?>" >
            <?php service('ShowLinksLibrary')->show_links_on_mobile_devices(isset($loadedPage) ? $loadedPage : (isset($showingPage) ? $showingPage : '')); ?>
                
            
                <div class="float-right">
                    Student management system <i>[SSMS]</i> 
                </div>
                <div>
                    <strong><?=lang('Student.copyright');?></strong> 
                    &copy;
                    <a class="text-navy"><?=esc(strval(service('uri')->getHost()));?></a>
                    <?=(Date('Y'));?>
                </div>
        </div>
</div>
